<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LetterTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/letter/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/letter/new', 'POST', [], [
            'title' => 'title',
            'meta' => 'meta',
            'content' => 'content'
        ]);

        $this->assertTrue(isset($request));
        $this->assertEquals('title', $request->title);
        $this->assertEquals('meta', $request->meta);
        $this->assertEquals('content', $request->content);

        $id = $request->id;

        // edit
        $request = $this->request('/api/letter/' . $id, 'PUT', [], [
            'title' => 'title2',
            'meta' => 'meta2',
            'content' => 'content2'
        ]);

        $this->assertTrue(isset($request));
        $this->assertEquals('title2', $request->title);
        $this->assertEquals('meta2', $request->meta);
        $this->assertEquals('content2', $request->content);

        // show
        $request = $this->request('/api/letter/' . $id, 'GET');

        $this->assertTrue(isset($request));
        $this->assertEquals('title2', $request->title);
        $this->assertEquals('meta2', $request->meta);
        $this->assertEquals('content2', $request->content);

        // delete
        $request = $this->request('/api/letter/' . $id, 'DELETE');

        $this->assertEquals('DELETED', $request->msg);
    }
}
