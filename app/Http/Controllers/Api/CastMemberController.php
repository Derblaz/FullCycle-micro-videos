<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Http\Resources\CastMemberResource;
use App\Http\Controllers\Api\BasicCrudController;

class CastMemberController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules= [
            'name' => 'required|max:255',
            'type' => 'required|in:'.implode(',',[CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR])
        ];
    }

    public function model()
    {
        return CastMember::class;
    }


    public function rulesStore()
    {
        return $this->rules;
    }
    
    public function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resource()
    {
        return CastMemberResource::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

}
