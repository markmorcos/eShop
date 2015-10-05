<?php
class Engine
{
	var $page;
	var $data;
	function Engine($page)
	{
		global $action, $id;
		$this->page = $page;
		echo $this->showHeader();
		if ($action == "add" || $action == "edit") echo $this->showForm($action, $id);
		else echo $this->showRecords();
		echo $this->showFooter();
	}
	function showHeader()
	{
		global $db, $submit, $url, $id, $uploads;
		$page = $this->page;
		if($submit == "add" || $submit == "edit" || $submit == "delete")
		{
			foreach($page["fields"] as $field)
			{
				if($submit == "edit" && isset($field["edit"]) && !$field["edit"] || $field["type"] == "map") continue;
				if($field["type"] != "file" && $field["type"] != "image") $data[$field["name"]] = $_POST[$field["name"]];
			}
			if($page["reference"]) $data[$page["reference"]["foreign"]] = $page["reference"]["id"];
			if($submit == "add")
			{
				$id = $state = $db->insert($page["table"], $data);
			}
			elseif($submit == "edit")
			{
				$ref = $db->querySelectSingle('SELECT * FROM ' . $page["table"] . ' WHERE ' . $page["primary"] . ' = ' . $id);
				foreach($page["fields"] as $field)
					if(($field["type"] == "file" || $field["type"] == "image") && (isset($_POST['remove_' . $field["name"]]) && $_POST['remove_' . $field["name"]] || $_FILES[$field["name"]]["name"]))
					{
						@unlink("../" . $uploads["files"] . $field["destination"] . '/' . $ref[$field["name"]]);
						$data[$field["name"]] = "";
					}
				$state = $db->update($page["table"], $data, $page["primary"] . ' = ' . $id);
				if($state) $state = $ref[$page["primary"]];
			}
			else
			{
				$ref = $db->querySelectSingle('SELECT * FROM ' . $page["table"] . ' WHERE ' . $page["primary"] . ' = ' . $id);
				$state = $db->query('DELETE FROM ' . $page["table"] . ' WHERE ' . $page["primary"] . ' = ' . $id);
				foreach($page["fields"] as $field) if($field["type"] == "file" || $field["type"] == "image") @unlink("../" . $uploads["files"] . $field["destination"] . '/' . $ref[$field["name"]]);
			}
			foreach($page["fields"] as $field)
			{
 				if($state && $_FILES[$field["name"]]["name"] && ($field["type"] == "file" || $field["type"] == "image"))
				{
					@mkdir("../" . $uploads["files"] . $field["destination"] . '/', 0755, true);
					$file = $state . '.' . pathinfo($_FILES[$field["name"]]["name"], PATHINFO_EXTENSION);
					move_uploaded_file($_FILES[$field["name"]]["tmp_name"], "../" . $uploads["files"] . $field["destination"] . '/' . $file);
					@chmod("../" . $uploads["files"] . $field["destination"] . '/' . $file, 0644);
					$data[$field["name"]] = $file;
				}
			}
			$state = $db->update($page["table"], $data, $page["primary"] . ' = ' . $id);
			$_SESSION["submit"] = $state ? $submit : "error";
			header("Location: $url");
			die();
		}
		else $this->data = $db->querySelect('SELECT * FROM ' . $page["table"] . (isset($page["reference"]) ? ' WHERE ' . $page["reference"]["foreign"] . ' = ' . $page["reference"]["id"] : ''));
		$result = '';
		$result .= '<div class="container">' . "\n";
		$result .= '<div class="row">' . "\n";
		$result .= '<div class="col-md-10 col-md-offset-1 col-sm-12">' . "\n";
		$submit = $_SESSION["submit"];
		if($submit == "add") $result .= '<div class="alert alert-success">The record has been added successfully!</div>' . "\n";
		elseif($submit == "edit") $result .= '<div class="alert alert-info">The record has been edited successfully!</div>' . "\n";
		elseif($submit == "delete") $result .= '<div class="alert alert-danger">The record has been deleted successfully!</div>' . "\n";
		elseif($submit == "error") $result .= '<div class="alert alert-danger">Unable to perform this action!</div>' . "\n";
		elseif($submit == "invalid") $result .= '<div class="alert alert-danger">Incorrect username or password!</div>' . "\n";
		unset($_SESSION["submit"]);
		if(isset($page["reference"]))
		{
			$ref = $db->querySelectSingle('SELECT * FROM ' . $page["reference"]["table"] . ' WHERE ' . $page["reference"]["key"] . ' = ' . $page["reference"]["id"]);
			$result .= '<h1 class="page-header">' . "\n";
			$result .= '<a href="' . $url . '">' . $page["title"] . ' - ' . $ref[$page["reference"]["value"]] . '</a>' . "\n";
			$result .= '<a class="btn btn-xs btn-default" href="' . addParameter($url, "action", "add") . '">Add</a>' . "\n";
			$result .= '</h1>' . "\n";
		}
		else
		{
			$result .= '<h1 class="page-header">' . "\n";
			$result .= '<a href="' . $url . '">' . $page["title"] . '</a>' . "\n";
			$result .= '<a class="btn btn-xs btn-default" href="' . addParameter($url, "action", "add") . '">Add</a>' . "\n";
			$result .= '</h1>' . "\n";
		}
		return $result;
	}
	function showForm($action, $id)
	{
		global $db, $uploads, $current_admin;
		$page = $this->page;
		$record = $db->querySelectSingle('SELECT * FROM ' . $page["table"] . ' WHERE ' . $page["primary"] . ' = \'' . $id . '\';');
		$validate = '';
		$validate .= '<script>' . "\n";
		$validate .= 'function reset_form()' . "\n";
		$validate .= '{' . "\n";
		foreach(array_reverse($page["fields"]) as $field) if(isset($field["required"]) && $field["required"] && (!isset($field["edit"]) || $field["edit"]) && ($action == "add" || $action == "edit" && $field["type"] != "file" && $field["type"] != "image")) $validate .= '$("#' . $field['name'] . '_error").addClass("invisible");' . "\n";
		$validate .= '}' . "\n";
		$validate .= 'function validate_form()' . "\n";
		$validate .= '{' . "\n";
		$validate .= 'reset_form();' . "\n";
		$validate .= 'var state = true;' . "\n";
		foreach(array_reverse($page["fields"]) as $field)
		{
			if(isset($field["required"]) && $field["required"] && (!isset($field["edit"]) || $field["edit"]) && ($action == "add" || $action == "edit" && $field["type"] != "file" && $field["type"] != "image"))
			{
				$validate .= 'if($("#' . $field["name"] . '").val() == "")' . "\n";
				$validate .= '{' . "\n";
				$validate .= '$("#' . $field["name"] . '").focus();' . "\n";
				$validate .= '$("#' . $field["name"] . '_error").removeClass("invisible");' . "\n";
				$validate .= 'state = false;' . "\n";
				$validate .= '}' . "\n";
			}
		}
		$validate .= 'return state;' . "\n";
		$validate .= '}' . "\n";
		$validate .= '</script>' . "\n";
		$form = '';
		$form .= '<form method="post" onsubmit="return validate_form();" onreset="reset_form();" enctype="multipart/form-data">' . "\n";
		$form .= '<input type="hidden" name="id" value="' . $record[$page["primary"]] . '">' . "\n";
		$form .= '<input type="hidden" name="submit" value="' . $action . '">' . "\n";
		if($page["reference"]) $form .= '<input type="hidden" name="' . $page["reference"]["foreign"] . '" value="' . $page["reference"]["id"] . '">' . "\n";
		$form .= '<table class="table">' . "\n";
		foreach($page["fields"] as $field)
		{
			if($action == "edit" && isset($field["edit"]) && !$field["edit"]) continue;
			if($field["type"] != "hidden")
			{
				$form .= '<tr>' . "\n";
				$form .= '<th>' . $field["title"] . '</th>' . "\n";
				$form .= '<td>' . "\n";
			}
			switch($field["type"])
			{
				case "hidden":
					$form .= '<input type="hidden" name="' . $field["name"] . '" value="' . $current_admin[$field["value"]] . '">' . "\n";
					break;
				case "image":
					$form .= '<input type="file" id="' . $field["name"] . '" name="' . $field["name"] . '" value="' . $record[$field["name"]] . '" class="navbar-left">' . "\n";
					if($record[$field["name"]])
					{
					  $form .= '<a href="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '" target="_blank"><img class="img-thumbnail" style="max-width:100px; max-height:100px" data-src="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '" src="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '"></a><br>';
					  $form .= '<label for="remove_' . $field["name"] . '">Remove</label>' . "\n";
					  $form .= '<input type="checkbox" id="remove_' . $field["name"] . '" name="remove_' . $field["name"] . '">' . "\n";
					}
					break;
				case "date":
					$form .= '<input type="" id="' . $field["name"] . '" name="' . $field["name"] . '" class="datepicker" value="' . $record[$field["name"]] . '">' . "\n";
					break;
				case "textarea":
					$form .= '<textarea id="' . $field["name"] . '" name="' . $field["name"] . '">' . $record[$field["name"]] . '</textarea>' . "\n";
					break;
				case "editor":
					$form .= '<div class="btn-toolbar" data-role="editor-toolbar" data-target=".editor">' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="icon-text-height"></i>&nbsp;<b class="caret"></b></a>' . "\n";
					$form .= '<ul class="dropdown-menu">' . "\n";
					$form .= '<li><a data-edit="fontSize 5"><font size="5">Huge</font></a></li>' . "\n";
					$form .= '<li><a data-edit="fontSize 3"><font size="3">Normal</font></a></li>' . "\n";
					$form .= '<li><a data-edit="fontSize 1"><font size="1">Small</font></a></li>' . "\n";
					$form .= '</ul>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="icon-bold"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="icon-italic"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="strikethrough" title="Strikethrough"><i class="icon-strikethrough"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="icon-underline"></i></a>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default" data-edit="insertunorderedlist" title="Bullet list"><i class="icon-list-ul"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="insertorderedlist" title="Number list"><i class="icon-list-ol"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="icon-indent-left"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="indent" title="Indent (Tab)"><i class="icon-indent-right"></i></a>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="icon-align-left"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="icon-align-center"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="icon-align-right"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="icon-align-justify"></i></a>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="icon-link"></i></a>' . "\n";
					$form .= '<div class="dropdown-menu input-append">' . "\n";
					$form .= '<input class="span2" placeholder="URL" type="text" data-edit="createLink"/>' . "\n";
					$form .= '<button class="btn" type="button">Add</button>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="unlink" title="Remove Hyperlink"><i class="icon-cut"></i></a>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div class="btn-group">' . "\n";
					$form .= '<a class="btn btn-default" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="icon-undo"></i></a>' . "\n";
					$form .= '<a class="btn btn-default" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="icon-repeat"></i></a>' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<input type="text" data-edit="inserttext" id="voiceBtn" x-webkit-speech="">' . "\n";
					$form .= '</div>' . "\n";
					$form .= '<div for="' . $field["name"] . '" class="editor">' . $record[$field["name"]] . '</div>' . "\n";
					$form .= '<textarea id="' . $field["name"] . '" name="' . $field["name"] . '" class="hidden">' . $record[$field["name"]] . '</textarea>' . "\n";
					break;
				case "select":
					$form .= '<select id="' . $field["name"] . '" name="' . $field["name"] . '">' . "\n";
					if(isset($field["reference"]))
					{
						$refs = $db->querySelect('SELECT * FROM ' . $field["reference"]["table"] . ' ORDER BY ' . $field["reference"]["value"]);
						$form .= '<option value="">(Default)</option>' . "\n";
						foreach($refs as $ref)
						{
							$selected = $ref[$field["reference"]["key"]] == $record[$field["name"]] ? " selected" : "";
							$form .= '<option value="' . $ref[$field["reference"]["key"]] . '"' . $selected . '>' . $ref[$field["reference"]["value"]] . '</option>' . "\n";
						}
					}
					else
					{
						$form .= '<option value="">(Default)</option>' . "\n";
						foreach($field["options"] as $option)
						{
							$selected = $option["key"] == $record[$field["name"]] ? " selected" : "";
							$form .= '<option value="' . $option["key"] . '"' . $selected . '>' . $option["value"] . '</option>' . "\n";
						}
					}
					$form .= '</select>' . "\n";
					break;
				case "map":
					$form .= '<div id="map-canvas" style="width: 100%; height: 300px;"></div>' . "\n";
					break;
				default:
					if(isset($field["edit"]) && !$field["edit"]) $form .= $record[$field["name"]] . "\n";
					else $form .= '<input type="' . $field["type"] . '" id="' . $field["name"] . '" name="' . $field["name"] . '" value="' . ($record[$field["name"]] ? $record[$field["name"]] : $field["value"]) . '">' . "\n";
					break;
			}
			if(isset($field["required"]) && $field["required"]) $form .= '<div id="' . $field["name"] . '_error" class="btn btn-danger disabled invisible">This field is required</div>' . "\n";
			if($field["type"] != "hidden")
			{
				$form .= '</td>' . "\n";
				$form .= '</tr>' . "\n";
			}
		}
		$form .= '<tr>' . "\n";
		$form .= '<td colspan="2">' . "\n";
		$form .= '<input class="btn btn-success" type="submit" value="Submit">' . "\n";
		$form .= '<input class="btn btn-default" type="reset" value="Reset">' . "\n";
		$form .= '</td>' . "\n";
		$form .= '</tr>' . "\n";
		$form .= '</table>' . "\n";
		$form .= '</form>' . "\n";
		$result = $validate . $form;
		return $result;
	}
	function showRecords()
	{
		global $db, $path, $url, $uploads;
		$page = $this->page;
		$data = $this->data;
		$result = '';
		if(!$data)
		{
			$result .= '<h5>No records yet.</h5>' . "\n";
		}
		else
		{
			$result .= '<table class="table" width="100%">' . "\n";
			$result .= '<thead>' . "\n";
			$result .= '<tr>' . "\n";
			$count = 0;
			foreach($page["fields"] as $field) if((!isset($field["display"]) || $field["display"]) && $field["type"] != "hidden") ++$count;
			$width = 100.0 / $count;
			foreach($page["fields"] as $field) if((!isset($field["display"]) || $field["display"]) && $field["type"] != "hidden") $result .= '<th width="' . $width . '%">' . $field["title"] . '</th>' . "\n";
			$result .= '<th colspan="2">Options</th>' . "\n";
			$result .= '</tr>' . "\n";
			$result .= '</thead>' . "\n";
			$result .= '<tbody>' . "\n";
			foreach($data as $record)
			{
				$result .= '<tr>' . "\n";
				foreach($page["fields"] as $field)
				{
					if(isset($field["display"]) && !$field["display"] || $field["type"] == "hidden") continue;
					if($field["type"] == "select")
					{
						if(isset($field["reference"]))
						{
							$ref = $db->querySelectSingle('SELECT * FROM ' . $field["reference"]["table"] . ' WHERE ' . $field["reference"]["key"] . ' = ' . $record[$field["name"]]);
							$result .= '<td>' . $ref[$field["reference"]["value"]] . '</td>' . "\n";
						}
						else
						{
							$display = 'N/A';
							foreach($field["options"] as $option) if($option["key"] == $record[$field["name"]]) $display = $option["value"];
							$result .= '<td>' . $display . '</td>' . "\n";
						}
					}
					elseif($field["type"] == "file")
					{
						$result .= '<td><a href="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '" target="_blank">View</a></td>' . "\n";
					}
					elseif($field["type"] == "image")
					{
						$result .= ($record[$field["name"]] ? '<td><a href="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '" target="_blank"><img class="img-thumbnail" style="max-width:100px; max-height:100px" data-src="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '" src="' . "../" . $uploads["files"] . $field["destination"] . '/' . $record[$field["name"]] . '"></a></td>' : '<td>N/A</td>') . "\n";
					}
					else $result .= '<td>' . $record[$field["name"]] . '</td>' . "\n";
				}
				$result .= '<td><a class="btn btn-xs btn-default" href="' . addParameters($url, array(array('action', 'edit'), array('id', $record[$page["primary"]]))) . '">Edit</a></td>' . "\n";
				$result .= '<td><a class="btn btn-xs btn-default" href="' . addParameters($url, array(array('submit', 'delete'), array('id', $record[$page["primary"]]))) . '" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a></td>' . "\n";
				foreach($page["options"] as $option) $result .= '<td><a class="btn btn-xs btn-default" href="' . str_replace('FOREIGN', $record[$page["primary"]], $option["url"]) . '">' . $option["title"] . '</a></td>' . "\n";
				$result .= '</tr>' . "\n";
			}
			$result .= '</tbody>' . "\n";
			$result .= '</table>' . "\n";
		}
		return $result;
	}
	function showFooter()
	{
		$result = '';
		$result .= '</div>' . "\n";
		$result .= '</div>' . "\n";
		$result .= '</div>' . "\n";
		return $result;
	}
}
?>
