<?php

namespace App\Traits;


use OhMyBrew\ShopifyApp\Models\Shop;

/**
 * Trait GraphQLTrait.
 */
trait GraphQLTrait
{
    protected $requestTimestamp;
    protected $leakRate = 50; // Leak rate per second
    protected $buffer = .1; // buffer seconds

    protected function graph($shop, string $query, array $parameters = [])
    {
        $queryCost = $this->calculateQueryCost($query, $parameters);
        $availableCost = $shop->api()->getApiCalls('graph', 'left');

        $availableCostSince = $this->requestTimestamp;
        $extraSecondsSince = round(microtime(true) - $availableCostSince, 3);
        $availableCost += ($extraSecondsSince * $this->leakRate);
        \Log::info("---------------------------");
        \Log::info($queryCost." > ".$availableCost);
        if ($queryCost > $availableCost) {
            $requireExtraCost = $queryCost - $availableCost;
            $awaitSeconds = $requireExtraCost / $this->leakRate;

            $awaitSeconds = ceil($awaitSeconds) + 1;
            \Log::info("sleep: ". ceil($awaitSeconds));
            sleep($awaitSeconds);
        }
        $data = $shop->api()->graph($query, $parameters);
        $this->requestTimestamp = end($data->timestamps);
        return $data;
    }

    protected function calculateQueryCost(string $query, array $variable = [])
    {
        try {
            $initialCost = 2;
            $queryArray = $this->parseGraphQLtoArray($query, $variable);
            $firstLimit = null;
            $isFirst = true;
            $queryCost = $initialCost; // Added Initial Cost
            foreach ($queryArray as $key => $value) {
                if ($firstLimit == null) {
                    preg_match('/first:(.*)\d/', $key, $firstLimit);
                    $firstLimit = (int)str_replace('first:', '', $firstLimit[0]);
                }
                if (is_array($value) && !isset($value['edges']['node'])) {
                    $queryCost += $firstLimit; // Added any operational
                }
                if (is_array($value) && isset($value['edges']['node'])) {
                    $subArray = $value['edges']['node'];
                    $queryCost += $this->calculateNodes($subArray, $key, $isFirst, $firstLimit, $initialCost); // Child Nodes
                }
                if ($isFirst) {
                    $isFirst = false;
                }
            }
            $queryCost += $firstLimit; // Added First Limit
            return $queryCost;
        } catch (\Exception $exception) {
            return 0;
        }
    }

    protected function parseGraphQLtoArray($query, array $variables = [])
    {
        $query = preg_replace('/^[query](.*?)\)/', '', $query, 1);
        if (count($variables)) {
            foreach ($variables as $key => $variable) {
                $query = preg_replace('/\$' . $key . '/', $variable, $query);
            }
        }
        $query = str_replace('"', '\"', $query);
        $query = str_replace("{", "{\n", $query);
        $query = str_replace("}", "\n}\n", $query);
        $query = array_map("trim", explode("\n", $query));
        foreach ($query as $k => $line) {
            // strip comments
            $line = explode("#", $line);
            $line = $line[0];
            // skip opening or closing tags
            if ($line === "{" || $line === "") {
                continue;
            }
            // declare as object value
            if (strpos($line, "{") !== false) {
                $name = trim(str_replace("{", "", $line));
                $query[$k] = '"' . $name . '": {';
                continue;
            }
            if (strpos($line, "}") !== false) {
                $query[$k] .= ',';
                continue;
            }
            $query[$k] = '"' . $line . '": true,';
        }
        $query = implode("", $query);
        // cut last comma
        $query = substr($query, 0, -1);
        // cut trailing commas
        $query = str_replace(",}", "}", $query);
        // produce php array
        $retval = json_decode($query, true);
        if (is_null($retval)) {
            throw new \Exception(sprintf("Error when parsing GraphQL fields: '%s'", $query));
        }
        return $retval;
    }

    protected function calculateNodes(array $array, $arrayKey, $isFirst, $firstLimit, $initialCost, $queryCost = 0)
    {
        if (!$isFirst) {
            preg_match('/first:(.*)\d/', $arrayKey, $limit);
            $limit = (int)str_replace('first:', '', $limit[0]);
            $queryCost += ($initialCost * $firstLimit + ($firstLimit * $limit));
        }
        foreach ($array as $key => $value) {
            if (is_array($value) && !isset($value['edges']['node'])) {
                $queryCost += $firstLimit;
            }
            if (is_array($value) && isset($value['edges']['node'])) {
                $subArray = $value['edges']['node'];
                return $this->calculateNodes($subArray, $key, false, $firstLimit, $initialCost, $queryCost);
            }
        }

        return $queryCost;
    }
}

