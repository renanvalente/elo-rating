# Elo Rating PHP
A PHP class which implements the [Elo rating system](https://en.wikipedia.org/wiki/Elo_rating_system).

# Install with composer

`composer require renanvalente/elo-rating dev-master`

Link to Packagist.org: https://packagist.org/packages/renanvalente/elo-rating

# Usage

    use 'RenanValente/Rating/Rating';
    // require 'src/Rating/Rating.php';

    // player A elo = 1000
    // player B elo = 2000
    // player A lost
    // player B win
    // kfactor default system 32

    $rating = new Rating(1000, 2000, Rating::LOST);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . $results['b'] . "<br>";

    // player A elo = 1000
    // player B elo = 2000
    // player A draw
    // player B draw
    // kfactor default system 32

    $rating = new Rating(1000, 2000);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . $results['b'] . "<br>";

    // player A elo = 1000
    // player B elo = 2000
    // player A win
    // player B lost
    // kfactor default system 32

    $rating = new Rating(1000, 2000, Rating::WIN);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . $results['b'] . "<br>";

    // player A elo = 1500
    // player B elo = 2000
    // player A lost
    // player B win
    // kfactor config rules

    $rating = new Rating(
        1500, // player A elo
        2000, // player B elo
        Rating::LOST, // player A lost
        [
            'default' => 32, // KFactor default
            'rules' => [  // Sample league scoring system 
                1000 => 25, // Bronze
                2000 => 16, // Silver
                3000 => 10, // Gold
                4500 => 5, // Diamond
            ]
        ]);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . $results['b'] . "<br>";

    // player A elo = 980
    // players B elo = [2000, 1000, 1500, 2200]
    // player A won all
    // player B everyone lost
    // kfactor config rules

    $rating = new Rating(
        980, // player A elo
        [2000, 1000, 1500, 2200], // player B elo
        Rating::WIN, // player A won all
        [
            'default' => 32, // KFactor default
            'rules' => [  // Sample league scoring system 
                1000 => 25, // Bronze
                2000 => 16, // Silver
                3000 => 10, // Gold
                4500 => 5, // Diamond
            ]
        ]);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . implode(' | ', $results['b']) . "<br>";

    // player A elo = 1500
    // players B elo = [2000, 1000, 1500, 2200]
    // player A config rules
    // player B config rules
    // kfactor config rules

    $rating = new Rating(
        1500, // player A elo
        [2000, 1000, 1500, 2200], // player B elo
        [Rating::WIN, Rating::LOST, Rating::DRAW, Rating::WIN], // player A won all
        [
            'default' => 32, // KFactor default
            'rules' => [  // Sample league scoring system 
                1000 => 25, // Bronze
                2000 => 16, // Silver
                3000 => 10, // Gold
                4500 => 5, // Diamond
            ]
        ]);

    $results = $rating->getNewRatings();

    echo "New rating for player A: " . $results['a'] . "<br>";
    echo "New rating for player B: " . implode(' | ', $results['b']) . "<br>";
    
---------------------------------------

# Credits

Developed by <a href="https://github.com/renanvalente/" rel="nofollow">Renan Valente</a> based on the development of <a href="https://github.com/Chovanec/elo-rating" rel="nofollow">Michal Chovanec</a>.