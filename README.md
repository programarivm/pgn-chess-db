## Chess Data

[![Build Status](https://travis-ci.org/programarivm/chess-data.svg?branch=master)](https://travis-ci.org/programarivm/chess-data)

<p align="center">
	<img src="https://github.com/programarivm/php-chess/blob/master/resources/chess-board.jpg" />
</p>

This repo provides you with CLI tools to manage a [PHP Chess](https://github.com/programarivm/pgn-chess) database of PGN games as well as to train a supervised learning model with [Rubix ML](https://github.com/RubixML/ML).

The supervised learning process is all about using [suitable heuristics](https://github.com/programarivm/php-chess/tree/master/src/Heuristic) such as king safety, attack, material or connectivity, among others. But how can we measure the efficiency of a given chess heuristic? This is where plotting data on nice charts comes to the rescue!

For further information on how to visually study the supervised data please visit [Heuristics Quest](https://github.com/programarivm/heuristics-quest).

### Set Up

Create an `.env` file:

    $ cp .env.example .env

Start the Docker containers:

	$ docker-compose up --build

Find out the IP of your MySQL container and update the `DB_HOST` in your `.env` file accordingly:

	$ docker inspect -f '{{range .NetworkSettings.Networks}}{{.Gateway}}{{end}}' chess_data_mysql

### Command Line Interface (CLI)

#### `cli/db/create.php`

Create the `chess` database with the `games` table containing STR tag pairs and movetexts:

    $ php cli/db/create.php
    This will remove the current chess database and the data will be lost.
    Do you want to proceed? (Y/N): y

Once the command above is successfully run, the `games` table will look as described next:

```text
mysql> use chess;
Database changed
mysql> describe games;
+----------+--------------------+------+-----+---------+----------------+
| Field    | Type               | Null | Key | Default | Extra          |
+----------+--------------------+------+-----+---------+----------------+
| id       | mediumint unsigned | NO   | PRI | NULL    | auto_increment |
| Event    | char(64)           | YES  |     | NULL    |                |
| Site     | char(64)           | YES  |     | NULL    |                |
| Date     | char(16)           | YES  |     | NULL    |                |
| White    | char(32)           | YES  |     | NULL    |                |
| Black    | char(32)           | YES  |     | NULL    |                |
| Result   | char(8)            | YES  |     | NULL    |                |
| WhiteElo | char(8)            | YES  |     | NULL    |                |
| BlackElo | char(8)            | YES  |     | NULL    |                |
| ECO      | char(8)            | YES  |     | NULL    |                |
| movetext | varchar(3072)      | YES  |     | NULL    |                |
+----------+--------------------+------+-----+---------+----------------+
11 rows in set (0.01 sec)

mysql>
```

Alternatively, add a heuristic picture too for further supervised training:

    $ php cli/db/create.php --heuristics

Once the command above is successfully run, the `games` table will look as described next:

```text
mysql> use chess
Database changed
mysql> describe games;
+-------------------+--------------------+------+-----+---------+----------------+
| Field             | Type               | Null | Key | Default | Extra          |
+-------------------+--------------------+------+-----+---------+----------------+
| id                | mediumint unsigned | NO   | PRI | NULL    | auto_increment |
| Event             | char(64)           | YES  |     | NULL    |                |
| Site              | char(64)           | YES  |     | NULL    |                |
| Date              | char(16)           | YES  |     | NULL    |                |
| White             | char(32)           | YES  |     | NULL    |                |
| Black             | char(32)           | YES  |     | NULL    |                |
| Result            | char(8)            | YES  |     | NULL    |                |
| WhiteElo          | char(8)            | YES  |     | NULL    |                |
| BlackElo          | char(8)            | YES  |     | NULL    |                |
| ECO               | char(8)            | YES  |     | NULL    |                |
| movetext          | varchar(3072)      | YES  |     | NULL    |                |
| heuristic_picture | json               | YES  |     | NULL    |                |
+-------------------+--------------------+------+-----+---------+----------------+
12 rows in set (0.00 sec)

mysql>
```

A so-called heuristic picture consists of a group of heuristic snapshots such as attack, center or material, among others. It is intended to capture the current state of a chess game at any given time, and can be plotted on a chart for further visual study. Heuristic pictures are mainly used for supervised training. For further information, please look at the programmer-defined heuristic evaluation functions available at [programarivm/pgn-chess/src/Heuristic/](https://github.com/programarivm/pgn-chess/tree/master/src/Heuristic).

#### `cli/db/seed.php`

Seed the `games` table with STR tag pairs and movetexts:

	$ php cli/db/seed.php data/players/Adams.pgn
	This will search for valid PGN games in the file.
	Large files (for example 50MB) may take a few seconds to be inserted into the database.
	Do you want to proceed? (Y/N): y
	15 games did not pass the validation.
	3234 games out of a total of 3249 are OK.

Once the command above is successfully run, this is how the game with `id = 1` looks like:

```text
mysql> SELECT * FROM games WHERE id = 1;
+----+----------------+--------+------------+----------------+-----------------+--------+----------+----------+------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| id | Event          | Site   | Date       | White          | Black           | Result | WhiteElo | BlackElo | ECO  | movetext                                                                                                                                                                                                                                                                                                                                                              |
+----+----------------+--------+------------+----------------+-----------------+--------+----------+----------+------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
|  1 | Lloyds Bank op | London | 1984.??.?? | Adams, Michael | Sedgwick, David | 1-0    |          |          | C05  | 1.e4 e6 2.d4 d5 3.Nd2 Nf6 4.e5 Nfd7 5.f4 c5 6.c3 Nc6 7.Ndf3 cxd4 8.cxd4 f6 9.Bd3 Bb4+ 10.Bd2 Qb6 11.Ne2 fxe5 12.fxe5 O-O 13.a3 Be7 14.Qc2 Rxf3 15.gxf3 Nxd4 16.Nxd4 Qxd4 17.O-O-O Nxe5 18.Bxh7+ Kh8 19.Kb1 Qh4 20.Bc3 Bf6 21.f4 Nc4 22.Bxf6 Qxf6 23.Bd3 b5 24.Qe2 Bd7 25.Rhg1 Be8 26.Rde1 Bf7 27.Rg3 Rc8 28.Reg1 Nd6 29.Rxg7 Nf5 30.R7g5 Rc7 31.Bxf5 exf5 32.Rh5+ 1-0 |
+----+----------------+--------+------------+----------------+-----------------+--------+----------+----------+------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)
```

Alternatively, seed the `games` table with STR tag pairs, movetexts and heuristic snapshots too for further supervised training:

	$ php cli/db/seed.php data/players/Adams.pgn --heuristics

Once the command above is successfully run, this is how the heuristic picture of the game with `id = 1` looks like:

```text
mysql> SELECT heuristic_picture FROM games WHERE id = 1;
+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| heuristic_picture                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| {"b": [[0.5, 1, 0, 0.97, 0, 0], [0.5, 1, 0, 0.87, 0.06, 0.17], [0.5, 1, 0, 0.94, 0.06, 0.17], [0.5, 1, 0.08, 0.97, 0.12, 0.17], [0.5, 1, 0.08, 1, 0.12, 0.33], [0.5, 1, 0.08, 0.94, 0.12, 0.33], [0.66, 1, 0.15, 0.94, 0.29, 0.33], [0.5, 1, 0.08, 0.9, 0.18, 0.33], [0.5, 1, 0.08, 0.74, 0.29, 0.5], [0.5, 1, 0.08, 0.65, 0.29, 0.5], [0.66, 1, 0.15, 0.61, 0.29, 0.5], [0.5, 0.67, 0.08, 0.65, 0.35, 0.67], [0.5, 0.67, 0.08, 0.61, 0.41, 0.83], [1, 0.67, 0.08, 0.39, 0.71, 0.83], [0.36, 0.67, 0.32, 0.32, 0.41, 1], [0.36, 0.67, 0.75, 0.23, 0.65, 0.67], [0.52, 0.67, 1, 0.19, 0.71, 0.67], [0.36, 0.33, 1, 0.19, 0.76, 0.83], [0.36, 0.33, 0.4, 0.13, 0.82, 0.67], [0.36, 0.33, 0.4, 0.26, 0.65, 0.5], [0.36, 0.33, 0.23, 0.29, 0.53, 1], [0.36, 0.33, 0.23, 0.19, 0.47, 0.67], [0.36, 0.67, 0.23, 0.19, 0.59, 0.5], [0.36, 0.67, 0.23, 0.19, 0.59, 0.5], [0.36, 0.33, 0.23, 0.19, 0.65, 0.5], [0.36, 0.33, 0.23, 0.26, 0.53, 0.5], [0.36, 0.33, 0.23, 0.26, 0.71, 0.5], [0.36, 0.33, 0.23, 0.26, 0.71, 0.33], [0.2, 0, 0.23, 0.1, 0.76, 0.33], [0.2, 0.33, 0.23, 0.13, 0.71, 0.33], [0.22, 0.33, 0.23, 0, 0.71, 0.33], [0.22, 0, 0.23, 0, 0.76, 0.33]], "w": [[0.5, 1, 0.08, 0.87, 0.06, 0], [0.5, 1, 0.15, 0.77, 0.35, 0.17], [0.5, 1, 0.15, 0.84, 0.18, 0.17], [0.5, 1, 0.15, 0.84, 0.24, 0], [0.5, 1, 0.15, 0.87, 0.24, 0.17], [0.5, 1, 0.15, 0.9, 0.35, 0.17], [0.34, 1, 0, 0.9, 0.29, 0.17], [0.5, 1, 0.08, 0.97, 0.29, 0.17], [0.5, 0.67, 0.15, 0.84, 0.47, 0.33], [0.5, 0.67, 0.15, 0.84, 0.47, 0.5], [0.34, 0.67, 0.08, 0.74, 0.41, 0.5], [0.5, 0.67, 0.15, 0.74, 0.65, 0.33], [0.5, 1, 0.15, 0.77, 0.76, 0.17], [0, 0.33, 0.15, 0.45, 0.71, 0.5], [0.64, 0.67, 0.08, 0.35, 0.82, 0.5], [0.64, 0.67, 0.08, 0.26, 0.88, 0.33], [0.48, 0.67, 0, 0.45, 0.59, 0.33], [0.64, 0.33, 0, 0.45, 0.53, 0.17], [0.64, 1, 0, 0.39, 0.65, 0.17], [0.64, 1, 0.08, 0.42, 0.41, 0.33], [0.64, 0.67, 0.15, 0.42, 0.47, 0.33], [0.64, 0.67, 0.15, 0.32, 0.47, 0.33], [0.64, 0.67, 0.08, 0.32, 0.35, 0.17], [0.64, 0.67, 0.08, 0.26, 0.47, 0.33], [0.64, 0.67, 0.08, 0.23, 0.47, 0.5], [0.64, 0.67, 0.08, 0.23, 0.47, 0.5], [0.64, 0.67, 0.08, 0.23, 0.53, 0.5], [0.64, 0, 0.08, 0.23, 0.59, 0.5], [0.8, 0, 0.08, 0.13, 0.59, 0.67], [0.8, 0, 0.08, 0.16, 0.53, 0.5], [0.78, 0, 0.08, 0.06, 0.76, 0.33], [0.78, 0, 0.08, 0.03, 1, 0.5]]} |
+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.01 sec)

mysql>
```

#### `cli/pgn/validate.php`

Validates that the PGN syntax in a text file is correct:

	$ php cli/pgn/validate.php data/players/Akobian.pgn
	This will search for syntax errors in the PGN file.
	Large files (for example 50MB) may take a few seconds to be parsed. Games not passing the validation will be printed.
	Do you want to proceed? (Y/N): y
	1353 games out of a total of 1353 are OK.

#### `cli/play/beginner.php`

Play with the `beginner.model`:

	$ php cli/play/beginner.php
	Prediction: 570.13386056267
	Decoded: c6

#### `cli/prepare/beginner.php`

Create the `1_100_beginner.csv` dataset with the games identified with an ID ranging from `1` to `100`:

	$ php cli/prepare/beginner.php 1 100

Once the command above is successfully run, this is how the `dataset/1_100_beginner.csv` file looks like:

```text
0;0;0;0;0;0;0;0.5;1;0.08;0.87;0.06;0;110129
0;0;0;0;0;0;0;0.5;1;0.15;0.77;0.35;0.17;110042
0;0;0;0;0;0;0;0.5;1;0.15;0.84;0.18;0.17;112191
0;0;0;0;1;1;0;0.5;1;0.15;0.84;0.24;0;117586
0;0;0;0;0;0;0;0.5;1;0.15;0.87;0.24;0.17;120123
0;0;0;0;0;0;0;0.5;1;0.15;0.9;0.35;0.17;118281
0;0;0;0;0;1;0;0.34;1;0;0.9;0.29;0.17;134291
0;1;0;0;0;1;0;0.5;1;0.08;0.97;0.29;0.17;118319
0;0;0;0;0;1;0;0.5;0.67;0.15;0.84;0.47;0.33;117445
0;0;0;0;0;1;0;0.5;0.67;0.15;0.84;0.47;0.5;114682
0;0;0;1;0;1;0;0.34;0.67;0.08;0.74;0.41;0.5;125877
...
```

#### `cli/train/beginner.php`

Train the `beginner.model` with the `1_100_beginner.csv` dataset:

	$ php cli/train/beginner.php 1_100_beginner.csv
	[2020-08-02 15:32:14] beginner.INFO: Learner init MLP Regressor {hidden_layers: [0: Dense {neurons: 100, alpha: 0, bias: true, weight_initializer: He, bias_initializer: Constant {value: 0}}, 1: Activation {activation_fn: ReLU}, 2: Dense {neurons: 100, alpha: 0, bias: true, weight_initializer: He, bias_initializer: Constant {value: 0}}, 3: Activation {activation_fn: ReLU}, 4: Dense {neurons: 50, alpha: 0, bias: true, weight_initializer: He, bias_initializer: Constant {value: 0}}, 5: Activation {activation_fn: ReLU}, 6: Dense {neurons: 50, alpha: 0, bias: true, weight_initializer: He, bias_initializer: Constant {value: 0}}, 7: Activation {activation_fn: ReLU}], batch_size: 128, optimizer: RMS Prop {rate: 0.001, decay: 0.1}, alpha: 0.001, epochs: 100, min_change: 1.0E-5, window: 3, hold_out: 0.1, cost_fn: Least Squares, metric: R Squared}
	[2020-08-02 15:32:14] beginner.INFO: Training started
	[2020-08-02 15:32:25] beginner.INFO: Epoch 1 R Squared=0.94634926347514 Least Squares=94238.878402936
	[2020-08-02 15:32:37] beginner.INFO: Epoch 2 R Squared=0.96649146513525 Least Squares=1142.1373874594
	[2020-08-02 15:32:51] beginner.INFO: Epoch 3 R Squared=0.97117243174116 Least Squares=1185.2010264815
	[2020-08-02 15:33:04] beginner.INFO: Epoch 4 R Squared=0.94546383822505 Least Squares=1062.9393909794
	[2020-08-02 15:33:17] beginner.INFO: Epoch 5 R Squared=0.97429089706881 Least Squares=1021.7216523576
	[2020-08-02 15:33:29] beginner.INFO: Epoch 6 R Squared=0.96820369201207 Least Squares=1013.5754980441
	[2020-08-02 15:33:41] beginner.INFO: Epoch 7 R Squared=0.9778175516864 Least Squares=899.41283826994
	[2020-08-02 15:33:52] beginner.INFO: Epoch 8 R Squared=0.96860370301558 Least Squares=918.29175554535
	[2020-08-02 15:34:02] beginner.INFO: Epoch 9 R Squared=0.97610453676813 Least Squares=929.87241344354
	[2020-08-02 15:34:14] beginner.INFO: Epoch 10 R Squared=0.97839266028844 Least Squares=906.37782887962
	[2020-08-02 15:34:25] beginner.INFO: Epoch 11 R Squared=0.95885220810568 Least Squares=871.08193382194
	[2020-08-02 15:34:37] beginner.INFO: Epoch 12 R Squared=0.95919889786785 Least Squares=865.28135340945
	[2020-08-02 15:34:47] beginner.INFO: Epoch 13 R Squared=0.97794930923409 Least Squares=880.09646901067
	[2020-08-02 15:34:47] beginner.INFO: Parameters restored from snapshot at epoch 10.
	[2020-08-02 15:34:47] beginner.INFO: Training complete

### Bash Scripts

#### `bash/load.sh`

Load STR tag pairs and movetexts from all PGN files stored in the data folder:

	$ bash/load.sh
	This will load all PGN files stored in the data folder. Are you sure to continue? (y|n) y

	1002 games did not pass the validation.
	104023 games out of a total of 105025 are OK.
	Loading games for 593 s...
	The loading of games is completed.

Load STR tag pairs, movetexts and heuristic snapshots too:

	$ bash/load.sh --heuristics

### License

The GNU General Public License.

### Contributions

Would you help make this library better? Contributions are welcome.

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "Chess Data Contributions"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Many thanks.
