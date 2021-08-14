<?php

    namespace App\Http\Controllers;

    use App\Models\Transaction;
    use Illuminate\Http\Request;

    class TransactionController extends Controller
    {

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function list()
        {
            try
            {
                $returnCode = 200;
                $data = Transaction::select([
                            'key',
                            'value',
                            'created_at',
                            Transaction::raw('UNIX_TIMESTAMP(created_at) as created_at_timestamp')
                        ])
                        ->get();
            }
            catch (\Exception $e)
            {
                /* Exceptions handling: database exception */
                $data = [];
                $returnCode = 500;
            }
            return response()->json($data, $returnCode);
        }

        /**
         * Return the latest key (by key)
         *
         * @return JSON {key, value}
         */
        public function find(Request $request, $key)
        {
            try
            {
                $timestamp = $request->query('timestamp');

                $query = Transaction::select([
                            'key',
                            'value'
                        ])
                        ->where('key', '=', $key);

                if (!empty($timestamp))
                {
                    $query->whereRaw('UNIX_TIMESTAMP(created_at) = ' . $timestamp);
                }

                $returnCode = 200;
                $data = $query->latest()
                        ->first();

                /* Exceptions handling: record not found */
                if (!$data)
                {
                    $data = [];
                    $returnCode = 404;
                }
            }
            catch (\Exception $e)
            {
                /* Exceptions handling: database exception */
                $data = [];
                $returnCode = 500;
            }
            return response()->json($data, $returnCode);
        }

        /**
         * Create key + value record
         *
         * @return JSON {key, value}
         */
        public function create(Request $request)
        {
            try
            {
                /*

                  Methods in json()
                  (
                  [0] => __construct
                  [1] => all
                  [2] => keys
                  [3] => replace
                  [4] => add
                  [5] => get
                  [6] => set
                  [7] => has
                  [8] => remove
                  [9] => getAlpha
                  [10] => getAlnum
                  [11] => getDigits
                  [12] => getInt
                  [13] => getBoolean
                  [14] => filter
                  [15] => getIterator
                  [16] => count
                  )
                 */
                $returnCode = 200;

                $json = $request->json();
                $key = $json->get('key');
                $value = $json->get('value');

                $data = [
                    'key' => $key,
                    'value' => $value
                ];
                Transaction::create($data);
            }
            catch (\Exception $e)
            {
                /* Exceptions handling: database exception */
                $data = [
                    'message' => $e->getMessage()
                ];
                $returnCode = 500;
            }
            return response()->json($data, $returnCode);
        }

    }
