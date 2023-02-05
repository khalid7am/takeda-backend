<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedTagsTable extends Migration
{
    private $tags = [
        [
          'name' => 'Gasztroenterológia',
          'image' => 'topics/1.png',
        ],
        [
          'name' => 'Általános sebésze',
          'image' => 'topics/2.png',
        ],
        [
          'name' => 'Transzplantológia',
          'image' => 'topics/3.png',
        ],
        [
          'name' => 'Infektológia',
          'image' => 'topics/4.png',
        ],
        [
          'name' => 'Onkológia',
          'image' => 'topics/5.png',
        ],
        [
          'name' => 'Hematológia',
          'image' => 'topics/1.png',
        ],
        [
          'name' => 'Hemosztazeológia',
          'image' => 'topics/2.png',
        ],
        [
          'name' => 'Gyermekgyógyászat',
          'image' => 'topics/3.png',
        ],
        [
          'name' => 'Reumatológia',
          'image' => 'topics/4.png',
        ],
        [
          'name' => 'Nefrológia',
          'image' => 'topics/5.png',
        ],
        [
          'name' => 'Endokrinológia',
          'image' => 'topics/1.png',
        ],
        [
          'name' => 'Allergológia és Immunológia',
          'image' => 'topics/2.png',
        ],
        [
          'name' => 'Neurológia',
          'image' => 'topics/3.png',
        ],
        [
          'name' => 'Kardiológia',
          'image' => 'topics/4.png',
        ],
        [
          'name' => 'Orvosi genetika',
          'image' => 'topics/5.png',
        ],
        [
          'name' => 'Pulmonológia',
          'image' => 'topics/1.png',
        ],
        [
          'name' => 'Szemészet',
          'image' => 'topics/2.png',
        ],
        [
          'name' => 'Bőrgyógyászat',
          'image' => 'topics/3.png',
        ],
        [
          'name' => 'Aneszteziológia',
          'image' => 'topics/4.png',
        ],
        [
          'name' => 'Transzfuziológia',
          'image' => 'topics/5.png',
        ],
        [
          'name' => 'Nőgyógyászat',
          'image' => 'topics/1.png',
        ],
        [
          'name' => 'Pszichiátria',
          'image' => 'topics/2.png',
        ],
        [
          'name' => 'Fül-orr-gégészet',
          'image' => 'topics/3.png',
        ],
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tags as $tag) {
            \App\Models\Preference::create($tag);
        }
    }
}
