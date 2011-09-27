<?php
/*
 *=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 *  Copyright:
 *
 *  XMLNuke: A Web Development Framework based on XML.
 *
 *  Main Specification: Joao Gilberto Magalhaes, joao at byjg dot com
 * 
 *  This file is part of XMLNuke project. Visit http://www.xmlnuke.com
 *  for more information.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 *=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/

ModuleFactory::IncludePhp("panteonescolar", "classes/base/PanteonEscolarBaseModule.class.php");


class LoadErrorStructure
{
	/**
	*@var ErrorType
	*/
	public $error;
	/**
	*@var string
	*/
	public $moduleName;
	/**
	*@var string
	*/
	public $errorMessage;
	/**
	*@var string
	*/
	public $stackTrace;
}

class LoadError extends BaseModule
{
	/**
	 * Error Object
	 *
	 * @var XMLNukeException
	 */
	public $_errorObject;
	
	/**
	 * Title Page
	 *
	 * @var String
	 */
	public $_titlePage = "no title";
	
	/**
	 * Default Constructor
	 *
	 * @return LoadError
	 */
	public function LoadError()
	{}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $customArg
	 */
	public function CustomSetup($customArg)
	{
		$this->_errorObject = $customArg;
	}

	/**
	 * Return the LanguageCollection used in this module
	 *
	 * @return LanguageCollection
	 */
	public function WordCollection()
	{
		$myWords = parent::WordCollection();
		if (!$myWords->loadedFromFile())
		{
			// English Words
			$myWords->addText("en-us", "TITLE", "Module Load Error");
			// Portuguese Words
			$myWords->addText("pt-br", "TITLE", "Módulo Carregar Error");
		}
		return $myWords;
	}

	/**
	 * Returns if use cache
	 *
	 * @return False
	 */
	public function useCache() 
	{
		return false;
	}

	/**
	 * Output error message
	 *
	 * @return PageXml object
	 */
	public function CreatePage() 
	{		
		$myWords = $this->WordCollection();		
		$this->_titlePage = $this->getRelatedError($myWords);
		
		$xmlnukeDoc = new XmlnukeDocument($this->_titlePage, "");

		$xmlnukeDoc->setPageTitle("Erro Encontrado");
		$xmlnukeDoc->setAbstract("Erro Encontrado");
		
		$this->ShowErros($xmlnukeDoc);

                $blockHead   = new XmlBlockCollection(NULL, BlockPosition::Right);
                $blockInfo   = new XmlBlockCollection(NULL, BlockPosition::Left);

                $xmlnukeDoc->addXmlnukeObject($blockHead);
                $blockHead->addXmlnukeObject(new ErroDBXML($this->_context, "menuHead"));

                $xmlnukeDoc->addXmlnukeObject($blockInfo);
                $blockInfo->addXmlnukeObject(new ErroDBXML($this->_context, "listarDireita"));
 
		return $xmlnukeDoc->generatePage();

	}
	
	/**
	 * Show Erros
	 *
	 * @param XmlnukeDocument $xmlnukeDoc
	 * @return unknown
	 */
	protected function ShowErros($xmlnukeDoc)
	{
		$myWords = $this->WordCollection();	
		$errorList = XMLNukeErrorModule::ErrorWordCollection($this->_context);
		$block = new XmlBlockCollection($this->_titlePage, BlockPosition::Center);
		$completeErrorInfo = "";
		//if ($this->_errorObject->errorType == ErrorType::DataBase) 
		//{
		//	$completeErrorInfo = $this->_errorObject->sql;
		//}
		
		$paragraph = new XmlnukeUIAlert($this->_context, UIAlert::BoxAlert);
		$paragraph->addXmlnukeObject(new XmlnukeText($errorList->Value("MESSAGE", $this->_errorObject->moduleName)));
		$block->addXmlnukeObject($paragraph);
		$message = $this->_errorObject->getMessage();
		if ($message != "") 
		{
			$message = str_replace("{", "[", $message);
			$message = str_replace("}", "]", $message);
			//$paragraph = new XmlParagraphCollection();
			$paragraph->addXmlnukeObject(new XmlnukeBreakLine());
			$paragraph->addXmlnukeObject(new XmlnukeBreakLine());
			$paragraph->addXmlnukeObject(new XmlnukeText($errorList->Value("TEXT_ERROR_ORIGINAL"), true));
			$paragraph->addXmlnukeObject(new XmlnukeText(' ' . $message));
			//$block->addXmlnukeObject($paragraph);
		}

		//$paragraph = new XmlParagraphCollection();
		$paragraph->addXmlnukeObject(new XmlnukeBreakLine());
		$paragraph->addXmlnukeObject(new XmlnukeBreakLine());
		$paragraph->addXmlnukeObject(new XmlnukeText($errorList->Value("TEXT_ERROR_RELATED"), true));
		$paragraph->addXmlnukeObject(new XmlnukeText(' ' . $this->_errorObject->errorType . "[" . $this->_errorObject->getCode() . "]"));
		//$block->addXmlnukeObject($paragraph);
		
		$paragraph = new XmlParagraphCollection();
		$href = new XmlAnchorCollection("javascript:history.go(-1)", "");
		$href->addXmlnukeObject(new XmlnukeText($myWords->Value("GOBACK")));
		$paragraph->addXmlnukeObject($href);
		
		if ($this->_errorObject->showStackTrace)
		{
			$paragraph->addXmlnukeObject(new XmlnukeText(" | "));
			
			$stack = new XmlnukeUIAlert($this->_context, UIAlert::ModalDialog, $errorList->Value("TEXT_ERROR_STACKTRACE"));
			$stack->setDimensions("800");
			$stack->setOpenAction(UIAlertOpenAction::URL, $myWords->Value("SHOWSTACKTRACE"));
			$stack->addXmlnukeObject(new XmlnukeCode($this->_errorObject->backTrace, $errorList->Value("TEXT_ERROR_STACKTRACE")));
			$paragraph->addXmlnukeObject($stack);		
		}
		
		$block->addXmlnukeObject($paragraph);
		$xmlnukeDoc->addXmlnukeObject($block);
		
		return $xmlnukeDoc;
	}
	
	/**
	 * Get formated Error Message to a error code
	 *
	 * @param LanguageCollection $wordsErros
	 * @param string $customValue
	 * @return string
	 */
	public function getRelatedError()
	{
		$customValue = "";
		$errorList = XMLNukeErrorModule::ErrorWordCollection($this->_context);
		
		$code = $this->_errorObject->getCode();
		if ($code <= 0)
		{
			$code = $this->_errorObject->errorType;
		}
		if (!empty($code))
		{
			$message = $errorList->Value($code);
			if ($message == "[$code?]") {
				$message = ' ' . $errorList->Value("MSG_ERROR_UNKNOW");
			}
			return $message;
		}
		else
		{
			return $errorList->Value("MSG_UNEXPECTED_ERROR");
		}
	}
	
}
?>
