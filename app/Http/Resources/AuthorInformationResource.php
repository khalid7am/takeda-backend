<?php

namespace App\Http\Resources;

use App\Models\Author;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        assert($this->resource instanceof Author);

        return [
            'alkalmazott_tag' => $this->resource->alkalmazott_tag,
            'reszvenytulajdon' => $this->resource->reszvenytulajdon,
            'eloadoi_dij' => $this->resource->eloadoi_dij,
            'testuleti_reszvetel' => $this->resource->testuleti_reszvetel,
            'konzultacios_szerzodes' => $this->resource->konzultacios_szerzodes,
            'tovabbkepzesi_hozzajarulas' => $this->resource->tovabbkepzesi_hozzajarulas,
        ];
    }
}
