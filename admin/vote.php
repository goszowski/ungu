<?

require_once('prepend.php');

function main() {
	global $request, $session, $AdminTrnsl;
	$action = $request->getparameter("do");

	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	$request->setAttribute("currentLoggedUser", $currentLoggedUser);

switch($action) {
	case "set_active":
		$id = (int)$request->getParameter("id");
		$question = Question::findById($id);
		$question->setActive();
		header("Location: /admin/vote.php?msg=VOTE_SET_ACTIVE_OK");
		break;
	case "remove":
		$id = (int)$request->getParameter("id");
		$question = Question::findById($id);
		$question->remove();
		header("Location: /admin/vote.php?msg=VOTE_REMOVE_OK");
		break;
	case "removeanswer":
		$id = (int)$request->getParameter("id");
		$number = (int)$request->getParameter("number");
		$question = Question::findById($id);
		$question->removeAnswer($number);
		header("Location: /admin/vote.php?do=edit_question&id=".$question->id."&msg=VOTE_REMOVE_ANSWER_OK");
		break;
	case "addanswer":
		$id = (int)$request->getParameter("id");
		$answerStr = $request->getParameter("answer");
		$question = Question::findById($id);

		if (!$answerStr) {
			header("Location: /admin/vote.php?do=edit_question&id=".$question->id."&msg=VOTE_ADD_ANSWER_BAD");
		} else {
			$question->addAnswer($answerStr);
			header("Location: /admin/vote.php?do=edit_question&id=".$question->id."&msg=VOTE_ADD_ANSWER_OK");
		}

		break;
	case "edit_question":
		$id = (int)$request->getParameter("id");
		$question = Question::findById($id);
		$request->setAttribute("question", $question);
		usetemplate("vote/question_edit");
		break;
	case "update_question":
		$id = (int)$request->getParameter("id");
		$question = Question::findById($id);

		$questionStr = $request->getParameter("question");
		$answers = $request->getParameter("answers");
		$answersVoteCounts = $request->getParameter("answer_vote_counts");

		$wasErrors = false;
		if (!$questionStr) {
			$wasErrors = true;
		}
		for ($i = 0; $i < sizeof($question->answers); $i++) {
			if (!$answers[$i]) {
				$wasErrors = true;
				break;
			}
			$question->answers[$i] = $answers[$i];
			$question->answer_vote_counts[$i] = (int)$answersVoteCounts[$i];
		}

		if (!$wasErrors) {
			$question->question = $questionStr;
			$question->storeAll();
			header("Location: /admin/vote.php?do=edit_question&id=".$question->id."&msg=VOTE_UPDATE_OK");
		} else {
			header("Location: /admin/vote.php?do=edit_question&id=".$question->id."&msg=VOTE_UPDATE_BAD");
		}

		break;
	case "createquestion":
		$questionStr = $request->getParameter("question");
		if (!$questionStr) {
			header("Location: /admin/vote.php?msg=VOTE_CREATE_BAD");
		} else {
			$question = Question::createNew($questionStr);
			header("Location: /admin/vote.php?msg=VOTE_CREATE_OK");
		}
		break;
	default :
		usetemplate("vote/questions_list");
		break;
}
}
main();
?>