<?php
/*
 *=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 *  Copyright:
 *
 *  XMLNuke: A Web Development Framework based on XML.
 *
 *  Main Specification and Implementation: Joao Gilberto Magalhaes, joao at byjg dot com
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

class DbMySQLFunctions extends DbBaseFunctions
{
    private $sysTimeStamp = 'NOW()';

	function Concat($s1, $s2 = null)
	{
		$sql = "concat(";
	 	for ($i = 0, $numArgs = func_num_args(); $i < $numArgs ; $i++)
	 	{
	 		$var = func_get_arg($i);
	 		$sql .= ($i==0 ? "" : ",") . $var;
	 	}
	 	$sql .= ")";

	 	return $sql;
	}

	/**
	 * Given a SQL returns it with the proper LIMIT or equivalent method included
	 * @param string $sql
	 * @param int $start
	 * @param int $qty
	 * @return string
	 */
	function Limit($sql, $start, $qty)
	{
		if (strpos($sql, ' LIMIT ') === false)
		{
			return $sql .= " LIMIT $start, $qty ";
		}
		else
		{
			return $sql;
		}
	}

	/**
	 * Given a SQL returns it with the proper TOP or equivalent method included
	 * @param string $sql
	 * @param int $qty
	 * @return string
	 */
	function Top($sql, $qty)
	{
		return $this->Limit($sql, 0, $qty);
	}

	/**
	 * Return if the database provider have a top or similar function
	 * @return unknown_type
	 */
	function hasTop()
	{
		return true;
	}

	/**
	 * Return if the database provider have a limit function
	 * @return bool
	 */
	function hasLimit()
	{
		return true;
	}

    /**
	 * Format date column in sql string given an input format that understands Y M D
	 * @param string $fmt
     * @param string $col
     * @return string
     * @example $db->getDbFunctions()->SQLDate("d/m/Y H:i", "dtcriacao")
	 */
    function SQLDate($fmt, $col=false)
	{
		if (!$col) $col = $this->sysTimeStamp;
		$s = 'DATE_FORMAT('.$col.",'";
		$concat = false;
		$len = strlen($fmt);
		for ($i=0; $i < $len; $i++) {
			$ch = $fmt[$i];
			switch($ch) {
			case 'Y':
			case 'y':
				$s .= '%Y';
				break;
			case 'Q':
			case 'q':
				$s .= "'),Quarter($col)";

				if ($len > $i+1) $s .= ",DATE_FORMAT($col,'";
				else $s .= ",('";
				$concat = true;
				break;
			case 'M':
				$s .= '%b';
				break;

			case 'm':
				$s .= '%m';
				break;
			case 'D':
			case 'd':
				$s .= '%d';
				break;

			case 'H':
				$s .= '%H';
				break;

			case 'h':
				$s .= '%I';
				break;

			case 'i':
				$s .= '%i';
				break;

			case 's':
				$s .= '%s';
				break;

			case 'a':
			case 'A':
				$s .= '%p';
				break;

			default:

				if ($ch == '\\') {
					$i++;
					$ch = substr($fmt,$i,1);
				}
				$s .= $ch;
				break;
			}
		}
		$s.="')";
		if ($concat) $s = "CONCAT($s)";
		return $s;
	}

    /**
	 * Format a string to database readable format.
	 * @param string $date
     * @param DATEFORMAT $dateFormat
     * @return string
     * @example $db->getDbFunctions()->toDate('26/01/1974', DATEFORMAT::DMY);
	 */
	function toDate($date, $dateFormat, $hour = false)
	{
		return parent::toDate($date, $dateFormat, $hour);
	}

    /**
	 * Format a string from database to a user readable format.
	 * @param string $date
     * @param DATEFORMAT $dateFormat
     * @return string
     * @example $db->getDbFunctions()->toDate('26/01/1974', DATEFORMAT::DMY);
	 */
	function fromDate($date, $dateFormat, $hour = false)
	{
		return parent::fromDate($date, $dateFormat, $hour);
	}

	/**
	 *
	 * @param DBDataSet $dbdataset
	 * @param string $sql
	 * @param array $param
	 * @return int
	 */
	function executeAndGetInsertedId($dbdataset, $sql, $param)
	{
		$id = parent::executeAndGetInsertedId($dbdataset, $sql, $param);
		$it = $dbdataset->getIterator("select LAST_INSERT_ID() id");
		if ($it->hasNext())
		{
			$sr = $it->moveNext();
			$id = $sr->getField("id");
		}

		return $id;
	}

}

?>
