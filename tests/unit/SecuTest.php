<?php

/*
 * This file is part of Sёcu.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Models\Secu;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class SecuTest.
 */
class SecuTest extends TestCase
{
    /**
     * @var Secu
     */
    private $secu;

    /**
     * SetUp test case environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->secu = new Secu();
    }

    /** @test */
    public function it_can_store_secu()
    {
        $data = 'test-data';
        $secuCount = $this->secu->count();
        $this->post('s', ['data' => $data]);
        $this->assertCount($secuCount + 1, $this->secu->get());
    }

    /** @test */
    public function it_can_return_hash_on_store()
    {
        $data = 'test-data';
        $secu = $this->post('s', ['data' => $data])->response->content();
        $secu = json_decode($secu, true);
        $this->assertArrayHasKey('hash', $secu);
        $this->assertEquals(6, strlen($secu['hash']));
    }

    /** @test */
    public function it_can_destroy_secu_on_retrieve()
    {
        $data = 'test-data';
        $secuCount = $this->secu->count();
        $secu = $this->post('s', ['data' => $data])->response->content();
        $secu = json_decode($secu, true);
        $this->get("s/{$secu['hash']}");
        $this->assertEquals($secuCount, $this->secu->count());
    }

    /** @test */
    public function it_can_preserve_content_integrity()
    {
        $data = 'test-data';
        $secu = $this->post('s', ['data' => $data])->response->content();
        $secu = json_decode($secu, true);

        $retrievedSecu = $this->get("s/{$secu['hash']}")->response->content();
        $retrievedSecu = json_decode($retrievedSecu, true);

        $this->assertEquals($data, $retrievedSecu['data']);
    }
}
