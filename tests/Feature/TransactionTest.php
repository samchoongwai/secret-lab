<?php

    namespace Tests\Feature;

    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Illuminate\Foundation\Testing\WithFaker;
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Illuminate\Foundation\Testing\DatabaseTransactions;
    use Tests\TestCase;

    class TransactionTest extends TestCase
    {

        use DatabaseTransactions;
        /* Dedine a set of data/ objects to be inserted into database */

        protected $objectKeys = [
            'item-19xk5',
            'item-2pjq3',
            'item-3x5gh',
            'product-15543',
            'product-22234'
        ];
        protected $valueTemplate = 'OBJECT: %s|Serial: %d';

        private function createSampleRecords()
        {
            /*
              Create 3 x sets of objects (5 each), with a minimum 2 seconds aways (to prevent timestamp conflict)
             */
            for ($i = 0; $i < 3; $i++)
            {
                foreach ($this->objectKeys as $key)
                {
                    $value = sprintf($this->valueTemplate, $key, date('YmdHis'));
                    $data = [$key => $value];

                    /* API will return code 200 + [key, value] */
                    $response = $this->postJson('/object', $data);
                    $response->assertStatus(200)->assertJson($data);
                }
                sleep(1);
            }
        }

        /* End Point: /object */
        public function test_create()
        {
            $this->createSampleRecords();
        }

        /* End Point: /object */
        public function test_create_negative()
        {
            /*
              Create 1 x sets of objects with invalid JSON format
             */
            foreach ($this->objectKeys as $key)
            {
                $value = sprintf($this->valueTemplate, $key, date('YmdHis'));
                $data = [ 'key' => $key, 'value' => $value];

                /* API will return code 200 + [key, value] */
                $response = $this->postJson('/object', $data);
                $response->assertStatus(400);
            }
        }


        /* End Point: /opject/get_all_records */
        public function test_get_all_records()
        {
            $this->createSampleRecords();

            $response = $this->getJson('/object/get_all_records');
            $response->assertStatus(200);
        }

        /* End Point: /object/{key} */
        public function test_get_object()
        {
            $this->createSampleRecords();

            /*
              Retrieve all objects, and get the "latest" object (of each key)
             */
            $testObjects = [];
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();

            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $data = [$key => $value];
                $testObjects[$key] = [$key => $value];
            }

            /*
              Retrieve objects by key, and assert the value returned is the latest value
             */
            foreach ($testObjects as $key => $data)
            {
                $response = $this->getJson('/object/' . $key);
                $response->assertStatus(200)->assertJson($testObjects[$key]);
            }

        }

        /* End Point: /object/{key} */
        public function test_get_object_negative()
        {
            $this->createSampleRecords();

            /*
              Retrieve all objects, and get the "latest" object (of each key)
             */
            $testObjects = [];
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();

            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $data = [$key => $value];
                $testObjects[$key] = [$key => $value];
            }

             /* randomly add a number after each item name, to test negative case = 404 */
             foreach ($this->objectKeys as $key)
             {
                 $response = $this->getJson('/object/' . $key . rand());
                 $response->assertStatus(404);
             }

        }

        /* End Point: /object */
        public function test_get_object_with_timestamp()
        {
            $this->createSampleRecords();

            /*
              Retrieve each object and check returned item is correct
             */
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();
            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $timestamp = $object['created_at_timestamp'];
                $testObject = [$key => $value];

                $response = $this->getJson('/object/' . $key . '?timestamp=' . $timestamp);
                $response->assertStatus(200)->assertJson($testObject);
             }
        }

        public function test_get_object_with_timestamp_negative()
        {
            $this->createSampleRecords();

            /*
              Retrieve each object with a wrong time stamp
             */
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();
            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $timestamp = $object['created_at_timestamp'];

                $response = $this->getJson('/object/' . $key . '?timestamp=' . '9' . substr($timestamp, 1));
                $response->assertStatus(404);
             }
        }

    }
