<?php
/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once "Modules/SurveyQuestionPool/classes/class.SurveyQuestionEvaluation.php";
require_once "Customizing/global/plugins/Modules/SurveyQuestionPool/SurveyQuestions/PriorityQuestion/classes/class.PriorityQuestion.php";

/**
 * Survey question evaluation
 *
 * @author	Gabriel Comte <gc@studer-raimann.ch>
 * @ingroup ModulesSurveyQuestionPool
 */
class PriorityQuestionEvaluation extends SurveyQuestionEvaluation
{

	/**
	 * @param array                           $a_row
	 * @param int                             $a_user_id
	 * @param array|ilSurveyEvaluationResults $a_results
	 *
	 * @override
	 */
	public function addUserSpecificResults(array &$a_row, $a_user_id, $a_results) {
		// SRAG-GC: unclear what this method is used for, but it's abstract in the mother class, so we "implement it" here.
	}


	/**
	 * @param ilSurveyEvaluationResults $a_qres
	 * @param $a_user_id
	 *
	 * @return array
	 */
	public function parseUserSpecificResults($a_qres, $active_fi) {

			global $ilDB;

			$answers = array();

		/**
		 * $prioQuestion PriorityQuestion
		 */
		$priorityQuestion = $a_qres->getQuestion();

		return $priorityQuestion->getPriorityAnswers(PriorityQuestion::getAnswerId($active_fi, $priorityQuestion->getId()));
	}

	/**
	 * Parse answer data into results instance
	 *
	 * @param ilSurveyEvaluationResults $a_results
	 * @param array $a_answers
	 * @param SurveyCategories $a_categories
	 */
	protected function parseResults(ilSurveyEvaluationResults $a_results, array $a_answers, SurveyCategories $a_categories = null)
	{

		$priorityQuestion = $a_results->getQuestion();
		$pq_results = $priorityQuestion->getUserAnswers($this->getSurveyId());

		$answersGrouped = array();
		foreach($pq_results as $answer){
			if(!array_key_exists($answer, $answersGrouped)){
				$answersGrouped[$answer] = 0;
			}

			$answersGrouped[$answer] ++;
		}

		foreach ($answersGrouped as $groupedAnswer => $amount){

			// total answers given
			$totalAmountAnswers = count($pq_results);

			// percentage of how often *this* answer got chosen in comparison to all answers
			$answer_perc = $amount / $totalAmountAnswers;

			$survey_cat = new ilSurveyCategory($groupedAnswer);
			$a_results->addVariable(new ilSurveyEvaluationResultsVariable($survey_cat, $amount, $answer_perc));
		}

		$num_users_answered = sizeof($a_answers);

		$a_results->setUsersAnswered($num_users_answered);
		$a_results->setUsersSkipped($this->getNrOfParticipants() - $num_users_answered);
	}


	public function getResults()
	{
		$results = new ilSurveyEvaluationResults($this->question);
		$answers = $this->getAnswerData();

		$this->parseResults(
			$results,
			(array)$answers[0],
			method_exists($this->question, "getCategories")
				? $this->question->getCategories()
				: null
		);

		return $results;
	}

}