<?php

namespace ChessData\Cli\DataPrepare\Training;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Dotenv\Dotenv;
use Chess\Combinatorics\RestrictedPermutationWithRepetition;
use Chess\HeuristicPicture;
use Chess\ML\Supervised\Regression\LinearCombinationLabeller;
use Chess\PGN\Symbol;
use ChessData\Pdo;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;

class DataPrepareCli extends CLI
{
    const DATA_FOLDER = __DIR__.'/../../../../dataset/training/regression';

    protected function setup(Options $options)
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../../../');
        $dotenv->load();

        $options->setHelp('Creates a prepared CSV dataset in the dataset/training/regression folder.');
        $options->registerArgument('n', 'A random number of games to be queried.', true);
    }

    protected function main(Options $options)
    {
        $opt = key($options->getOpt());
        $filename = "black_{$options->getArgs()[0]}_".time().'.csv';

        $sql = "SELECT * FROM games WHERE result = '0-1'
            ORDER BY RAND()
            LIMIT {$options->getArgs()[0]}";

        $games = Pdo::getInstance()
                    ->query($sql)
                    ->fetchAll(\PDO::FETCH_ASSOC);

        $dimensions = (new HeuristicPicture(''))->getDimensions();

        $permutations = (new RestrictedPermutationWithRepetition())
            ->get(
                [ 8, 13, 21, 34],
                count($dimensions),
                100
            );

        $fp = fopen(self::DATA_FOLDER."/$filename", 'w');

        foreach ($games as $game) {
            try {
                $balance = (new HeuristicPicture($game['movetext']))->take()->getBalance();
                foreach ($balance as $val) {
                    $label = (new LinearCombinationLabeller($permutations))->balance($val);
                    $row = array_merge($val, [$label[Symbol::BLACK]]);
                    fputcsv($fp, $row, ';');
                }
            } catch (\Exception $e) {}
        }

        fclose($fp);
    }
}

$cli = new DataPrepareCli();
$cli->run();
