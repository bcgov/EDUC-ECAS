<?php

namespace Tests\Feature;

use App\Dynamics\ContractStage;

use Tests\TestCase;

class ContractStageTest extends TestCase
{

    /** @test */
    public function get_contract_stage_list()
    {
        $result = ContractStage::all();

        $this->assertIsArray($result);
        $this->assertIsArray($result[0]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
    }


}
