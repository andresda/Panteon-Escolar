<?php

/*
*
* Panteon Escolar
*
* Yuri Wanderley (yuri.wanderley at gmail.com)
* Tarcisio Araujo (tatauphp at gmail.com)
* Marcelo Soares Souza (marcelo at juntadados.org)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* http://www.gnu.org/licenses/gpl-2.0.html
*
*/

class MensagemUsuarioDBXML extends XmlnukeCollection implements IXmlnukeDocumentObject
{

    protected $_context;

    protected $_nome_entidade = "mensagem_usuario";
    protected $_nome_modulo = "mensagemusuario";
    protected $_titulo_entidade = "Mensagem do Usuário";
    protected $_num_registros_padrao = 5;

    public function criarProcessPageFields($id_usuario = "", $id_tema_panteon = "", $id_mensagem_usuario_resposta = "", $id_usuario_dest = "", $permissao = "")
    {

        // Inicio da Obtencao de dados de Tabelas Auxiliares-Relacionadas
        //
        $db = new UsuarioXTemaPanteonDB($this->_context);
        $it = $db->obterTodosOsUsuariosColetaramTemaPanteonPorIDTemaPanteon($id_tema_panteon);
        $arrayUsuarioDest = PanteonEscolarBaseDBAccess::getArrayFromIterator($it, "id_usuario", "nome_completo_usuario");
        unset($arrayUsuarioDest[$id_usuario]);
        array_push($arrayUsuarioDest, "Todos usuários deste Tema");
        //
        // Fim Obtencao de dados de Tabelas Auxiliares-Relacionadas

        // Inicio ProcessPageField
        $fieldList = new ProcessPageFields();

        // Inicio Campos da Entidade

        if($id_usuario_dest == "")
        {
            $field = ProcessPageFields::FactoryMinimal("id_usuario_dest", "Destino", 30, true, true);
            $field->fieldXmlInput = XmlInputObjectType::SELECTLIST;
            $field->arraySelectList = $arrayUsuarioDest;
            $fieldList->addProcessPageField($field);
        }

        $field = ProcessPageFields::FactoryMinimal("nome_mensagem_usuario", "Assunto", 64, true, true);
        $field->fieldXmlInput = XmlInputObjectType::TEXTBOX;
        $fieldList->addProcessPageField($field);

        $field = ProcessPageFields::FactoryMinimal("texto_mensagem_usuario", "Mensagem", 30, true, true);
        $field->fieldXmlInput = XmlInputObjectType::HTMLTEXT;
        $fieldList->addProcessPageField($field);

        if($id_usuario_dest != "")
        {
            $field = ProcessPageFields::FactoryMinimal("id_usuario_orig", "Remetente", 30, true, true);
            $field->fieldXmlInput = XmlInputObjectType::SELECTLIST;
            $field->arraySelectList = $arrayUsuarioDest;
            $fieldList->addProcessPageField($field);
        }

        else
        {
            $field = ProcessPageFields::FactoryMinimal("id_usuario_orig", "Usuário Remetente", 30, false, false);
            $field->fieldXmlInput = XmlInputObjectType::HIDDEN;
            $field->defaultValue = $id_usuario;
            $fieldList->addProcessPageField($field);
        }

        $field = ProcessPageFields::FactoryMinimal("id_mensagem_usuario_resposta", "Mensagem Resposta", 30, false, false);
        $field->fieldXmlInput = XmlInputObjectType::HIDDEN;
        $field->defaultValue = $id_mensagem_usuario_resposta;
        $fieldList->addProcessPageField($field);

        $field = ProcessPageFields::FactoryMinimal("data_hora_cadastro_mensagem_usuario", "Data/Hora Cadastro", 15, false, false);
        $field->fieldXmlInput = XmlInputObjectType::HIDDEN;
        $field->defaultValue = date("Y-m-d H:i:s");
        $fieldList->addProcessPageField($field);

        $field = ProcessPageFields::FactoryMinimal("id_tema_panteon", "Tema Panteon", 30, false, false);
        $field->fieldXmlInput = XmlInputObjectType::HIDDEN;
        $field->defaultValue = $id_tema_panteon;
        $fieldList->addProcessPageField($field);

        // ID da Entidade (Todos Possuem)
        $field = ProcessPageFields::FactoryMinimal("id_".$this->_nome_entidade, "", 1, false, false);
        $field->editable = false;
        $field->key = true;
        $fieldList->addProcessPageField($field);

        // Fim dos Campos do ProcessPageFields

        $processpage = new PanteonEscolarMyProcess($this->_context,
                $fieldList,
                $this->_titulo_entidade,
                "module:panteonescolar.".$this->_nome_modulo."&amp;chamada=1",
                NULL,
                $this->_nome_entidade,
                PanteonEscolarBaseDBAccess::DATABASE());

        if($permissao)
        {
            $processpage->setPermissions($permissao[0], $permissao[1], $permissao[2], $permissao[3]);
        }

        else
        {
            $processpage->setPermissions(false, false, false, false);
        }

        // Filtros
        $filtro = "";

        $filtro .= " id_tema_panteon = "  . $id_tema_panteon." ";

        if($id_usuario != "")
        {
            $filtro  = " id_tema_panteon = "  . $id_tema_panteon." ";
            $filtro .= " AND id_usuario_orig = "  . $id_usuario ." ";
        }

        if($id_usuario_dest != "")
        {
            $filtro  = " id_tema_panteon = "  . $id_tema_panteon." ";
            $filtro .= " AND id_usuario_dest = "  . $id_usuario_dest." ";

        }

        if($filtro != "")
        {
            $processpage->setFilter($filtro);
        }

        // Debug::PrintValue($filtro);

        return $processpage;

    }

    public function generateObject($current)
    {
        $span1 = new XmlnukeSpanCollection();
        $span1->addXmlnukeObject($this->criarProcessPageFields());
        $node = XmlUtil::CreateChild($current, $this->_nome_entidade, "");
        $body = XmlUtil::CreateChild($node, "body", "");
        parent::generatePage($body);

    }

    public function MensagemUsuarioDBXML($context, $nome_modulo = "mensagemusuario", $titulo = "Mensagem Usuário")
    {
        if(!($context instanceof Context))
        {
            throw new Exception("Falta de Context");
        }

        $this->_context = $context;
        $this->_nome_modulo = $nome_modulo;
        $this->_titulo_entidade = $titulo;

    }

}

?>
