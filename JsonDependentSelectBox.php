<?php

/**
 * @author Daniel Robenek
 * @license MIT
 */

namespace DependentSelectBox;

use Nette\Application\Responses\JsonResponse;
use Nette\Forms\Container as FormContainer;

// \Nette\Forms\FormContainer::extensionMethod("addJsonDependentSelectBox", "DependentSelectBox\JsonDependentSelectBox::formAddJsonDependentSelectBox");

class JsonDependentSelectBox extends DependentSelectBox
{

	public static $jsonResoponseItems = array();
        /**
         * @var \Nette\Application\UI\Presenter
         */
	public static $presenter;

	public function submitButtonHandler($button) {
		parent::submitButtonHandler($button);
		if ($this->getParent()->getPresenter()->isAjax())
			$this->addJsonResponseItem($this);
	}

	protected function addJsonResponseItem($selectBox) {
		self::$jsonResoponseItems[] = $selectBox;
		if($selectBox instanceof DependentSelectBox)
			foreach($selectBox->childs as $child)
				$child->addJsonResponseItem($child);
	}

	public static function tryJsonResponse() {
		if(empty(self::$jsonResoponseItems))
			return;

		$payload = array(
			"type" => "JsonDependentSelectBoxResponse",
			"items" => array()
		);
		foreach(self::$jsonResoponseItems as $item) {
			$payload["items"][$item->getHtmlId()] = array(
				"selected" => $item->getValue(),
				"items" => $item->getItems()
			);
		}
		$response = new JsonResponse($payload);
		self::$presenter->sendResponse($response);
	}


	/**
	 * @deprecated Alias for Container_prototype_addDependentSelectBox
	 */
	public static function formAddJsonDependentSelectBox($_this, $name, $label, $parents, $dataCallback) {
		self::$presenter = $_this->getPresenter();
		return self::Container_prototype_addJsonDependentSelectBox($_this, $name, $label, $parents, $dataCallback);
	}

	public static function Container_prototype_addJsonDependentSelectBox(FormContainer $obj, $name, $label, $parents, $dataCallback) {
		return $obj[$name] = new JsonDependentSelectBox($label, $parents, $dataCallback);
	}

	public static function register($methodName = "addJsonDependentSelectBox") {
		if(PHP_VERSION_ID >= 50300)
			FormContainer::extensionMethod($methodName, "DependentSelectBox\JsonDependentSelectBox::Container_prototype_addJsonDependentSelectBox");
		else
			FormContainer::extensionMethod("FormContainer::$methodName", array("JsonDependentSelectBox", "Container_prototype_addJsonDependentSelectBox"));
	}
	
}