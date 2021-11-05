<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WikiTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/wiki/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/wiki/new', 'POST', [], [
            'title' => 'Title',
            'body' => 'Body'
        ]);

        $this->assertTrue(isset($request));
        $this->assertIsString($request->id);
        $this->assertIsString($request->title);
        $this->assertIsString($request->body);

        $id = $request->id;

        // edit
        $request = $this->request('/api/wiki/' . $id, 'PUT', [], [
            'title' => 'Title2',
            'body' => 'Body2'
        ]);

        $this->assertTrue(isset($request));
        $this->assertIsString($request->id);
        $this->assertIsString($request->title);
        $this->assertIsString($request->body);

        // show
        $request = $this->request('/api/wiki/' . $id, 'GET');

        $this->assertTrue(isset($request));
        $this->assertIsString($request->id);
        $this->assertIsString($request->title);
        $this->assertIsString($request->body);

        // delete
        $request = $this->request('/api/wiki/' . $id, 'DELETE');

        $this->assertEquals('DELETED', $request->msg);
    }
}
