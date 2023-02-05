<?php

namespace App\Helpers\PPT;

use App\Helpers\ImageManipulationHelper;
use App\Models\ArticleAsSlide;
use App\Models\ArticleAsSlideImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use NcJoes\OfficeConverter\OfficeConverter;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Str;

class ArticlePptHelper
{
    public function uploadPpt(Request $request, $articleId = null)
    {
        // NEED TO STORE THE PPT FILE
        // GET THE FILE NAME
        $fileName = $request->file('file')->getClientOriginalName();
        // GET FILE NAME WITHOUT EXTENSION
        $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
        $fileNameExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        // SANITIZING THE FILENAME
        $fileNameWithoutExtension = str_replace(' ', '-', $fileNameWithoutExtension);
        $fileNameWithoutExtension = preg_replace('/[^A-Za-z0-9\-]/', '', $fileNameWithoutExtension);
        $fullFileName = $fileNameWithoutExtension . '.' . $fileNameExtension;
        // CREATE PDF FILE NAME
        $pdfName = $fileNameWithoutExtension . '.pdf';
        // CREATE FOLDER STRUCTURE
        $path = Carbon::now()->format('Ymd-hmi') . '/' . auth()->id();
        // CREATE THE FILE ON PPT_FILES DISK
        $file = Storage::disk('ppt_files')->putFileAs($path, $request->file('file'), $fullFileName);

        exec("chmod -R 0777 " . Storage::disk('ppt_files')->path($path));
        // CREATE A DATABASE LOG FOR SLIDE
        $articleAsSlide = ArticleAsSlide::create([
            'article_id' => $articleId,
            'path' => $path,
            'ppt_name' => $fullFileName,
            'pdf_name' => $pdfName,
            'unique_identifier' => Str::uuid(),
        ]);

        // RUN PPT TO PDF CONVERSION
        $this->convertPptToPdf($articleAsSlide);
        // RUN PDF TO IMAGE CONVERSION
        $canGo = $this->convertPdfToImage($articleAsSlide);

        // GIVE BACK THE SLIDE
        return $articleAsSlide;
    }

    // PPT TO PDF CONVERSION WITH THE HELP OF LIBREOFFICE
    public function convertPptToPdf(ArticleAsSlide $articleAsSlide)
    {
        // GET PPT
        $file = Storage::disk('ppt_files')->path($articleAsSlide->path .'/'. $articleAsSlide->ppt_name);
        $path = Storage::disk('ppt_files')->path($articleAsSlide->path);
        // CALL THE WIZARD
        try {
            $converter = new OfficeConverter($file);
            // DO THE ACTUAL MAGIC
            $converter->convertTo($articleAsSlide->pdf_name);
            // CALL THE NEXT STEP
        } catch (\Exception $e) {
            logger("Running: libreoffice --headless --convert-to pdf --outdir " . $path . " " . $file);
            logger(shell_exec("libreoffice --headless --convert-to pdf --outdir " . $path . " " . $file));
            logger("Done.");
        }
    }

    // PDF TO IMAGE CONVERSION WITH THE HELP OF SPATIE/PDF-TO-IMAGE
    public function convertPdfToImage($articleAsSlide)
    {
        // GET THE FILE
        $pdfFile = $articleAsSlide->path.'/'. $articleAsSlide->pdf_name;
        $file = Storage::disk('ppt_files')->path($pdfFile);
        // RUN THE MAGIC
        $pdf = new Pdf($file);
        foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
            // INIT FILE NAME
            $filePath = Storage::disk('ppt_files')->path($articleAsSlide->path);
            // SAVE THE IMAGE
            $pdf->setPage($pageNumber)
                ->saveImage($filePath);
            // SAVE THEM TO DATABASE
            $articleAsSlideImage = ArticleAsSlideImage::create([
                'article_as_slide_id' => $articleAsSlide->id,
                'path' => $articleAsSlide->path. '/' .$pageNumber . '.jpg',
                'order' => $pageNumber,
            ]);
        }

        // CELEBRATE
        return true;
    }

    // GET THE LATEST PPT BY USER & FILE NAME
    // WE CAN IMPLEMENT A MORE PROTETED SYSTEM WITH UUIDS AND SO ON
    // BESIDE WE CAN ALSO SUPPOSE THEY DONT UPLOAD THE SAME PPT TO ANOTHER ARTICLE
    // TODO: DISCUSS
    public function getArticleSlideByUserAndFile($uniqueId, $createdById = null)
    {
        if (!$createdById) {
            $createdById = auth()->id();
        }
        $articleAsSlide = ArticleAsSlide::createdById($createdById)->identifier($uniqueId)->latest()->first();

        $articleAsSlide->load('slideImages');

        return $articleAsSlide;
    }


    // THIS WILL HELP
    // AFTER SLIDE NUMBER +1 WILL BE THE QUESTION SLIDE
    // e.g. image slides: 1-20
    // $afterSlideNumber = 10
    // IF $slide->order > 10 (11, 12, 13....)
    // THE GET +1
    // 11 -> 12, 12 -> 13, 13->14
    // QUESTION CAN GET afterSlideNumber +1 which is 11 in this example
    public function saveArticleQuestionAsNewSlide($question, $afterSlideNumber, $uniqueId, $questionId, $createdById = null)
    {
        if (!$createdById) {
            $createdById = auth()->id();
        }
        // CREATE THE IMAGE FROM QUESTION
        $image = (new ImageManipulationHelper)->saveTextToImage($question);
        // GET THE ORIGINAL SLIDE
        $articleAsSlide = ArticleAsSlide::createdById($createdById)->identifier($uniqueId)->latest()->first();
        // LOAD THE IMAGE SLIDES
        $articleAsSlide->load('slideImages');
        // DETERMINE THE ORDER OF QUESTIONS
        $countSlideQuestions = 0;
        // LOOP THROUGH THE IMAGES
        foreach ($articleAsSlide->slideImages as $key => $slideImage) {
            // IF NUMBER IS BIGGER (ONLY BIGGER)
            if ($slideImage->order > $afterSlideNumber) {
                // ADD THEM TO +1
                // DON'T ASK WHY, BUT $model->increment() DIDN'T WORK FOR ME...
                // FOR QUESTION, WE CHANGE ALSO THE NAME TO THE NEW ORDER
                $slideImage->order +=1;
            }
            if (!is_null($slideImage->order_question)) {
                if ($slideImage->order > $afterSlideNumber) {
                    $slideImage->order_question += 1;
                } else {
                    $countSlideQuestions += 1;
                }
            }
            $slideImage->save();
        }
        // GIVE QUESTION IMAGE THE PROPER NUMBER
        $questionAsImageOrder = $afterSlideNumber + 1;
        // GIVE QUESTION IMAGE A FILE NAME
        // GIVE A RANDOM STRING TO AVOID SLIDES WITH THE SAME NAME
        $questionAsSlideFileName = 'question_'.($questionId ?? Str::random(8)).'.jpg';
        // STORE THE FILE
        $questionAsImageOrderJpg = Storage::disk('ppt_files')->put($articleAsSlide->path. '/'.$questionAsSlideFileName, $image);
        // STORE IT IN THE DATABASE
        $articleAsSlideImage = ArticleAsSlideImage::create([
            'article_as_slide_id' => $articleAsSlide->id,
            'path' => $articleAsSlide->path. '/' .$questionAsSlideFileName,
            'order' => $questionAsImageOrder,
            'order_question' => $countSlideQuestions+1,
        ]);

        $articleAsSlide->load('slideImages');

        return $articleAsSlide;
    }

    // SAME AS ABOVE
    // WITH MULTIPLE QUESTIONS & POSITIONS

    public function saveMultipleArticleQuestionAsNewSlide($questionArray = [], $uniqueId, $articleId = null, $createdById = null)
    {
        if (!$createdById) {
            $createdById = auth()->id();
        }
        // EXAMPLE
        // KEY IS = AFTER SLIDE ORDER
        // VALUE IS = QUESTION
        // $questionsArray = [
        //     3 => 'Which statement about lung disease related to AATD is true?',
        //     11 => 'Cheesecake croissant gummies donut wafer tiramisu candy canes pie biscuit. Halvah candy toffee lollipop dessert halvah sweet. Lemon drops marzipan bonbon cake gummies muffin.'
        // ];

        // TO MAKE IT THE PROPER WE NEED THE FOLLOWING
        // DESC ORDER BY KEY
        // SO THE FIRST MODIFICATION WON'T MAKE FAIL THE OTHERS

        // DESC SORT BY KEY
        $descSort = krsort($questionArray);


        foreach ($questionArray as $afterSlideNumber => $question) {

              // EXAMPLE
            // KEY IS = AFTER SLIDE ORDER
            // VALUE IS = QUESTION
            // $questionsArray = [
            //     3 => 'Which statement about lung disease related to AATD is true?',
            //     11 => 'Cheesecake croissant gummies donut wafer tiramisu candy canes pie biscuit. Halvah candy toffee lollipop dessert halvah sweet. Lemon drops marzipan bonbon cake gummies muffin.'
            // ];

            // TO MAKE IT THE PROPER WE NEED THE FOLLOWING
            // DESC ORDER BY KEY
            // SO THE FIRST MODIFICATION WON'T MAKE FAIL THE OTHERS

            // CREATE THE IMAGE FROM QUESTION
            $image = (new ImageManipulationHelper)->saveTextToImage($question);
            // GET THE ORIGINAL SLIDE
            $articleAsSlide = ArticleAsSlide::createdById($createdById)->identifier($uniqueId)->latest()->first();
            if ($articleId) {
                $articleAsSlide->article_id = $articleId;
                $articleAsSlide->save();
            }

            // LOAD THE IMAGE SLIDES
            $articleAsSlide->load('slideImages')->refresh();

            $countSlideQuestions = 0;

            // LOOP THROUGH THE IMAGES
            foreach ($articleAsSlide->slideImages as $key => $slideImage) {

                // IF NUMBER IS BIGGER (ONLY BIGGER)
                if ($slideImage->order > $afterSlideNumber) {
                    // ADD THEM TO +1
                    // DON'T ASK WHY, BUT $model->increment() DIDN'T WORK FOR ME...
                    $slideImage->order +=1;
                }
                if (!is_null($slideImage->order_question)) {
                    if ($slideImage->order > $afterSlideNumber) {
                        $slideImage->order_question += 1;
                        $countSlideQuestions += 1;
                    }
                }
                $slideImage->article_id = $articleId;
                $slideImage->save();
            }
            // GIVE QUESTION IMAGE THE PROPER NUMBER
            $questionAsImageOrder = $afterSlideNumber + 1;
            // GIVE QUESTION IMAGE THE PROPER NUMBER
            $questionAsSlideFileName = 'question_'.$questionAsImageOrder.'.jpg';
            // STORE THE FILE
            $questionAsImageOrderJpg = Storage::disk('ppt_files')->put($articleAsSlide->path. '/'.$questionAsSlideFileName, $image);
            // STORE IT IN THE DATABASE
            $articleAsSlideImage = ArticleAsSlideImage::create([
                'article_as_slide_id' => $articleAsSlide->id,
                'path' => $articleAsSlide->path. '/' .$questionAsSlideFileName,
                'order' => $questionAsImageOrder,
                'article_id' => $articleId,
                'order_question' => $countSlideQuestions+1,
            ]);
        }

        return $articleAsSlide;
    }

    public function deleteQuestionSlideFromArticle($uniqueId, $questionId, $createdById)
    {
        $articleAsSlide = ArticleAsSlide::createdById($createdById)->identifier($uniqueId)->latest()->first();
        $articleAsSlide->load('slideImages');

        $slide_path = $articleAsSlide->path. '/question_' .$questionId;
        $slide = ArticleAsSlideImage::where('path', $slide_path)->firstOrfail();

        foreach ($articleAsSlide->slideImages as $key => $slideImage) {
            if ($slideImage->order > $slide->order) {
                $slideImage->order -=1;
            }

            if (!is_null($slideImage->order_question)) {
                if ($slideImage->order_question > $slide->order_question) {
                    $slideImage->order_question -= 1;
                }
            }
            $slideImage->save();
        }

        Storage::disk('ppt_files')->delete($slide->path);
        $slide->delete();

        $articleAsSlide->load('slideImages');

        return $articleAsSlide;
    }

    public function updateQuestionSlideFromArticle($question, $uniqueId, $questionId, $createdById)
    {
        $articleAsSlide = ArticleAsSlide::createdById($createdById)->identifier($uniqueId)->latest()->first();
        $articleAsSlide->load('slideImages');

        $slide_path = $articleAsSlide->path. '/question_' .$questionId. '.jpg';
        $slide = ArticleAsSlideImage::where('path', $slide_path)->firstOrfail();

        $image = (new ImageManipulationHelper)->saveTextToImage($question);
        Storage::disk('ppt_files')->put($slide->path, $image);

        $articleAsSlide->load('slideImages');

        return $articleAsSlide;
    }

    public function downloadPpt($slideImages, $filename)
    {
        $images = $slideImages->pluck('path')->all();
        $urls = [];

        foreach ($images as $key => $image) {
            $urls[$key] = Storage::disk('ppt_files')->path($image);
        }

        $file = (new ImageManipulationHelper)->imagesToPdf($urls, $filename);

        return $file;
    }
}
