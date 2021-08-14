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

        /* End Point: /object */

        public function test_create()
        {
            /*
              Create 3 x sets of objects (5 each), with a minimum 2 seconds aways (to prevent timestamp conflict)
             */
            for ($i = 0; $i < 3; $i++)
            {
                foreach ($this->objectKeys as $key)
                {
                    $value = sprintf($this->valueTemplate, $key, date('YmdHis'));
                    $data = ['key' => $key, 'value' => $value];

                    /* API will return code 200 + [key, value] */
                    $response = $this->postJson('/object', $data);
                    $response->assertStatus(200)
                            ->assertJson($data);
                    ;
                }
                sleep(2);
            }
            // }

            /* End Point: /object/get_all_records */

            // public function test_list()
            // {
            $response = $this->getJson('/object/get_all_records');
            $response->assertStatus(200);
            // }

            /* End Point: /object/{key} */

            // public function test_find()
            //{
            /*
              Retrieve all objects, and get the "latest" object (of each key)
             */
            $testObjects = [];
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();

            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $data = ['key' => $key, 'value' => $value];
                $testObjects[$key] = $data;
            }

            /*
              Retrieve objects by key, and assert the value returned is the latest value
             */
            foreach ($testObjects as $key => $data)
            {
                $response = $this->getJson('/object/' . $key);
                $response->assertStatus(200)->assertJson($data);
            }
            // }

            /* End Point: /object/{key} */

            //public function test_find_negative()
            //{
            /* randomly add a number after each item name, to test negative case = 404 */
            foreach ($this->objectKeys as $key)
            {
                $response = $this->getJson('/object/' . $key . rand());
                $response->assertStatus(404);
            }
            //}

            /* End Point: /object/{key}?timestamp={timestamp} */

            //public function test_find_by_timestamp()
            //{
            /*
              Retrieve all objects, and retrieve each object by key + timestamp to assert the value returned is correct (base on the timestamp)
             */

            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();
            foreach ($availableObjects as $object)
            {

                $key = $object['key'];
                $value = $object['value'];
                $timestamp = $object['created_at_timestamp'];
                $data = ['key' => $key, 'value' => $value];

                $response = $this->getJson('/object/' . $key . '?timestamp=' . $timestamp);
                $response->assertStatus(200)
                        ->assertJson($data);
                ;
            }
            //}

            /* End Point: /object/{key}?timestamp={timestamp} */

            //public function test_find_by_timestamp_negative()
            //{
            /*
              Retrieve all objects, and retrieve each object by key + timestamp (modified) to assert no value is returned due to invalid timestamp
             */
            $availableObjects = $this->getJson('/object/get_all_records')->decodeResponseJson()->json();
            foreach ($availableObjects as $object)
            {
                $key = $object['key'];
                $value = $object['value'];
                $timestamp = $object['created_at_timestamp'];
                $data = ['key' => $key, 'value' => $value];

                $response = $this->getJson('/object/' . $key . '?timestamp=' . '9' . substr($timestamp, 1));
                $response->assertStatus(404);
                ;
            }
            //}
        }

    }
    