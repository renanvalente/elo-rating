<?php

/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Renan Valente <renan.a.valente@gmail.com>
 */
 
namespace RenanValente\Rating;

class Rating
{
    const KFACTOR = 32;
    const WIN = 1;
    const DRAW = 0.5;
    const LOST = 0;

    /**
     * @var int The K Factor used default.
     */
    private $_kfactors;

    /**
     * Protected & private variables.
     */
    protected $_ratingA;
    protected $_ratingB;
    
    protected $_scoreA;
    protected $_scoreB;

    protected $_expectedA;
    protected $_expectedB;

    protected $_newRatingA;
    protected $_newRatingB;

    /**
     * Constructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int $ratingA Current rating of A
     * @param int|array $ratingB Current rating of B
     * @param int|array $scoreA Score of A
     * @param int|array $kfactor
     */
    public function  __construct($ratingA = null, $ratingB = null, $scoreA = self::DRAW, $kfactors = null)
    {
        $this->setKFactor($kfactors);

        if ($ratingA !== null && $ratingB !== null) {
            $this->setNewSettings($ratingA, $ratingB, $scoreA);
        }
    }

    /**
     * Set new input data.
     *
     * @param int $ratingA Current rating of A
     * @param int|array $ratingB Current rating of B
     * @param int|array $scoreA Score of A
     * @return self
     */
    public function setNewSettings($ratingA, $ratingB, $scoreA)
    {
        $this->_ratingA = $ratingA;
        $this->_ratingB = $ratingB;
        $this->_scoreA = $scoreA;

        if (!is_array($this->_ratingB)) {
            $expectedScores = $this -> _getExpectedScores($this->_ratingA, $this->_ratingB);
            $this -> _expectedA = $expectedScores['a'];
            $this -> _expectedB = $expectedScores['b'];
    
            $newRatings = $this->_getNewRatings($this->_ratingA, $this->_ratingB, $this->_expectedA, $this->_expectedB, (is_array($this->_scoreA)) ? reset($this->_scoreA) : $this->_scoreA);
            $this->_newRatingA = $newRatings['a'];
            $this->_newRatingB = $newRatings['b'];
    
            return $this;
        }

        $this->_newRatingA = $this->_ratingA;
        $this->_newRatingB = $this->_ratingB;

        foreach ($this->_ratingB as $key => $rating) {
            $expectedScores = $this->_getExpectedScores($this->_newRatingA, $rating);
            $this->_expectedA = $expectedScores['a'];
            $this->_expectedB = $expectedScores['b'];

            $newRatings = $this->_getNewRatings($this->_newRatingA, $rating, $this->_expectedA, $this->_expectedB, (is_array($this->_scoreA)) ? $this->_scoreA[$key] : $this->_scoreA);
            
            $this->_newRatingA = $newRatings['a'];
            $this->_newRatingB[$key] = $newRatings['b'];
        }

        return $this;

    }

    /**
     * Retrieve the calculated data.
     *
     * @return array An array containing the new ratings for A and B.
     */
    public function getNewRatings()
    {
        return array (
            'a' => $this -> _newRatingA,
            'b' => $this -> _newRatingB
        );
    }

    public function setKFactor($kfactors)
    {
        if (!is_array($kfactors)) {
            $this->_kfactors = $kfactors;
        } else {
            krsort($kfactors);
            $this->_kfactors = $kfactors;
        }
    }

    // Protected & private functions begin here

    /**
     * @param int $ratingA The Rating of Player A
     * @param int $ratingB The Rating of Player B
     * @return array
     */
    protected function _getExpectedScores($ratingA, $ratingB)
    {
        $expectedScoreA = 1 / ( 1 + ( pow( 10 , ( $ratingB - $ratingA ) / 400 ) ) );
        $expectedScoreB = 1 / ( 1 + ( pow( 10 , ( $ratingA - $ratingB ) / 400 ) ) );

        return array (
            'a' => $expectedScoreA,
            'b' => $expectedScoreB
        );
    }

    /**
     * @param int $ratingA The Rating of Player A
     * @param int $ratingB The Rating of Player A
     * @param int $expectedA The expected score of Player A
     * @param int $expectedB The expected score of Player B
     * @param int $scoreA The score of Player A
     * @return array
     */
    protected function _getNewRatings($ratingA, $ratingB, $expectedA, $expectedB, $scoreA)
    {
        $scoreB = ($scoreA === self::DRAW) ? self::DRAW : (int) !$scoreA;

        $newRatingA = $ratingA + ( $this->_getKFactor($ratingA) * ( $scoreA - $expectedA ) );
        $newRatingB = $ratingB + ( $this->_getKFactor($ratingA) * ( $scoreB - $expectedB ) );

        return [
            'a' => $newRatingA,
            'b' => $newRatingB
        ];
    }

    protected function _getKFactor($ratingA)
    {
        if (empty($this->_kfactors)) {
            return self::KFACTOR;
        }

        if (!is_array($this->_kfactors)) {
            return $this->_kfactors;
        }

        $_kfactor = $this->_kfactors['default'] ?? self::KFACTOR;
        if (!isset($this->_kfactors['rules'])) {
            return $_kfactor;
        }

        foreach ($this->_kfactors['rules'] as $limit => $kfactor) {
            if ($ratingA >= $limit) {
                $_kfactor = $kfactor;
            }
        }

        return $_kfactor;
    }

}
