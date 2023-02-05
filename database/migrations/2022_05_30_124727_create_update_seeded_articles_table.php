<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateUpdateSeededArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Article::getQuery()->delete();

        for ($i=0; $i < 6; $i++) { 
            $article = \App\Models\Article::create([
                'type' => 'LEG',
                'author_id' => 1,
                'title' => 'Fast Five Quiz: Presentation and Diagnosis of Alpha-'.$i.' Antitrypsin Deficiency',
                'excerpt' => 'Lörem ipsum sude ona plus keng såsom flyttstajla suprakav. Bessa poguligen prefuk pol. Donade ryr epining. Näsavirtad nänade megas popov svischa. Mäna nyd. Ponat fangen stenorening sepreheten i gåtåg. Dimost amoling i rånde epin kror. Infranat eskap, tiryning i kess. Mp3-spelare parakalig, ot, i poligen. Polysk oda i hexaskapet anartad. Jöbok ledoskade: sogäd. Reagen pobel blingbling och spebel, semiling. Revis terad. Dira. Geogisk dedylig bere muvynade. Kuminera mävis sekrovis, dymoren hexakäbelt. Vanade. Kontrar mire makross epide den soll.',
                'content' => "[{\"postThumbnail\":null,\"articleContent\":[{\"content\":\"Featuring 56 different cases, this essential text: Places learning in a practical context. Information about disease states is presented in case-based format which leads to better retention. Covers topics including congenital heart disease, coronary artery disease, cardiomyopathies, valvular heart disease, arrhythmias, heart failure, peripheral vascular disease, and more Designed to present important concepts and information in a unique way to complement textbook learning Features electrocardiograms, angiograms, and pressure tracings Is applicable to those working towards certification in Cardiovascular Disease from the American Board of Internal Medicine or preparing for board examinations in other countries Is also suitable for those requiring MOC recertification Features cases on aortic insufficiency, atrial fibrillation, Brugada syndrome, carotid artery disease, myocardial bridging, congenital heart disease, electrolyte abnormalities, apical HCM, mitral regurgitation,\",\"uuid\":\"2026f745-81a5-4333-a10e-523dcc189ac2\"},{\"content\":\"RV outflow tract tachycardia, pulmonary hypertension, arrhythmogenic right ventricular dysplasia, aortic stenosis, atrial myxoma, atrial tachycardia, pulmonic insufficiency, Takotsubo, tricuspid regurgitation, Wolfe-Parkinson-White syndrome, pulmonic stenosis, coronary anomalies, ECG changes of hypothermia, endocarditis, pulmonary embolus, ventricular septal defect, hemodynamics of hypertrophic cardiomyopathy, complete heart block, heart failure, coronary artery disease, atrial septal defect, constrictive pericarditis, fractional flow reserve, dextrocardia, STEMI, early repolarization, giant cell myocarditis, peripheral arterial disease, pericardial tamponade, peripheral arterial disease, pericarditis, myocarditis, long QT syndrome, mitral stenosis, tetralogy of Fallot, and supraventricular tachycardia among others. Cardiology Board Review offers fellows a fresh and engaging approach to the information required to achieve success in board examinations.Featuring 56 different cases, this essential Features cases on aortic insufficiency, atrial fibrillation, Brugada syndrome, carotid artery disease, myocardial bridging, congenital heart disease, electrolyte abnormalities, apical HCM, mitral regurgitation, RV outflow tract tachycardia, pulmonary hypertension, arrhythmogenic right ventricular dysplasia, aortic stenosis, atrial myxoma, atrial tachycardia, pulmonic insufficiency, Takotsubo, tricuspid regurgitation, Wolfe-Parkinson-White syndrome, pulmonic stenosis, coronary anomalies, ECG changes of hypothermia, endocarditis, pulmonary embolus, ventricular septal defect, hemodynamics of hypertrophic cardiomyopathy, complete heart block, heart failure, coronary artery disease, atrial septal defect, constrictive pericarditis, fractional flow reserve, dextrocardia, STEMI, early repolarization, giant cell myocarditis, peripheral arterial disease, pericardial tamponade, peripheral arterial disease, pericarditis, myocarditis, long QT syndrome, mitral stenosis, tetralogy of Fallot, and supraventricular tachycardia among others.\",\"uuid\":\"3f25a22d-3ef2-4acd-b1e9-6bf450b357ec\"}]}]",
                'media' => null,
                'publisher_id' => 1,
                'published_at' => Carbon::now()
            ]);

            $article->preferences()->attach([1,3]);

            $question1 = $article->questions()->create([
                'question' => 'Which statement about lung disease related to AATD is true?',
                'show_at' => null,
                'paragraph_uuid' => '2026f745-81a5-4333-a10e-523dcc189ac2',
                'order' => 0,
            ]);
            $question1->choices()->create([
                'answer' => "Wrong - PASC should be suspected if the patient's symptoms have not resolved after 6 weeks from the start of acute infection 1",
                'is_correct' => 0,
                'order' => 0
            ]);
            $question1->choices()->create([
                'answer' => "Wrong - PASC should be suspected if the patient's symptoms have not resolved after 6 weeks from the start of acute infection 2",
                'is_correct' => 0,
                'order' => 1
            ]);
            $question1->choices()->create([
                'answer' => "Correct - PASC should be suspected if the patient's symptoms have not resolved after 6 weeks from the start of acute infection 3",
                'is_correct' => 1,
                'order' => 2
            ]);
            $question1->reasons()->create([
                'content' => 'Patients with AATD are predisposed to lung disease. The most common pulmonary complication in patients with AATD is emphysema, which usually manifests earlier in life than non–AATD-related emphysema. AATD-related lung disease does not typically manifest until individuals are at least in their 30s, when respiratory symptoms, such as dyspnea, cough, and wheezing, may begin. However, these symptoms are nonspecific and are not pathognomonic of AATD, which contributes to the underdiagnosis of AATD. Learn more about the clinical presentation of AATD. Patients with AATD are predisposed to lung disease. The most common pulmonary complication in patients with AATD is emphysema, which usually manifests earlier in life than non–AATD-related emphysema. AATD-related lung disease does not typically manifest until individuals are at least in their 30s, when respiratory symptoms, such as dyspnea, cough, and wheezing, may begin. However, these symptoms are nonspecific and are not pathognomonic of AATD.',
                'order' => 0
            ]);
            $question1->images()->create([
                'path' => 'img/lungs.0bbe4202.png',
                'order' => 0
            ]);

            $question2 = $article->questions()->create([
                'question' => 'Which statement about lung disease related to AATD is true?',
                'show_at' => null,
                'paragraph_uuid' => '3f25a22d-3ef2-4acd-b1e9-6bf450b357ec',
                'order' => 1,
            ]);
            $question2->choices()->create([
                'answer' => "Correct - PASC should be suspected if the patient's symptoms have not resolved after 6 weeks from the start of acute infection 4",
                'is_correct' => 1,
                'order' => 0
            ]);
            $question2->choices()->create([
                'answer' => "Wrong - PASC should be suspected if the patient's symptoms have not resolved after 6 weeks from the start of acute infection 4",
                'is_correct' => 0,
                'order' => 1
            ]);
        }
    }
}
