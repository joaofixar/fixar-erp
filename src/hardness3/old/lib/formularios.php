<?
/**
 * Formulários
 */
class Formulario
{
	private $formNome = 'form_padrao';
	public $formArray = array();
	
	/**
	* Construtor - Define o nome único do formulário
	* 
	* @var string $formNome
	* @return true
	*/
	function __construct($formNome = '') {
    	$this->formNome = (!empty($formNome)) ? $formNome : $this->formNome;
   	}
	
	/**
	* Define o Forumulário
	* 
	* Documentação de todas as opções que podem ser utilizadas.
	* Campos do array definidos como 'opcional' só precisam ser definidos se precisar mudar o valor padrão. 
	* O valor padrão é o que está definido neste documento, exemplo: 'id_show' => true
	* 
	* <code>
	* array(
	* 	'campos' => array(
	* 		'id_do_campo' => array(						// (obrigatório) - usado no html 'id'
	* 			'name'			=> 'nome_do_campo',		// (obrigatório) - usado no html 'name'
	* 			'type' 			=> 'tipo_do_campo',		// (obrigatório) - pode ser um desses: text,password,radio,checkbox,hidden,textarea,select,file
	*  			
	* 			'id_show'		=> true,				// (opcional) - irá mostrar o campo html 'id'
	* 			'length'		=> array(10,20),		// (opcional) - se tipo for 'text': tamanho_do_campo,tamanho_máximo || se tipo for 'textarea': linhas,colunas (o padrão é não definir nada e utilizar o padrão do navegador)
	* 			'value'			=> '',					// (opcional) - preenchar o valor do campo
	* 			'extra'			=> '',					// (opcional) - pode ser utilizado para adicionar qualquer campo html extra no campo do formulário, exemplo: class="radio"
	* 			'array_group'	=> true,				// (opcional) - por padrão, todos os campos são agrupados em um array dentro do html, exemplo: form[nome_do_form]
	* 			
	* 			// somente válido para tipos: 'radio' e 'checkbox'
	* 			'checked'		=> false,				// (opcional) - checado
	* 			
	* 			// somente válido para tipo: 'textarea'
	* 			'disabled'		=> false,				// (opcional) - desabilitado
	* 			'readonly'		=> false,				// (opcional) - somente leitura
	* 			
	* 			// somente válido para tipo: 'select'
	* 			'select'		=> array(
	* 				array (
	*					'title		=> 'Option 1',		// (obrigatório) - título da opção
	* 					'value'		=> 1,				// (opcional) - se não for definido um valor, o retorno será o título da opção
	* 					'selected'	=> false,			// (opcional) - se a opção é para aparecer selecionada
	*				),
	* 				... várias opções  ...
	* 			),
	* 			
	* 			// validações automáticas
	* 			'reload'		=> true,				// (opcional) - irá automaticamente preencher os campos com o último valor digitado (em caso de erro)
	* 			'regx' 			=> '',					// (opcional) - irá validar o campo, possiveis valores são: nao_vazio, email, 'a sua própria expressão regular' ( irei adicionar mais validações com o tempo, se precisar de alguma é só pedir: tiago@hardness.com.br :) )
	* 			
	* 			// somente para uso interno, não utilizar
	* 			'processed'			=> true/false,		// (interno) - identifica se o campo já foi processado
	* 			'processed_value' 	=> '',				// (interno) - utilizado para pegar o valor com o campoValorEnviado() após um processo com sucesso
	* 			'validation'		=> true/false,		// (interno) - indica se um campo passou na validação do formulário
	* 		),
	* 		... vários campos ...
	* 	)
	* );
	* </code>
	* 
	* @param array $formArray
	* @return null
	*/
	public function definirFormularioArray($formArray)
	{
		$this->formArray = $formArray;
		return null;
	}
	
	/**
	* Gera o código HTML para um campo
	* 
	* @param string $id
	* @return string
	*/
	public function getCode($id)
	{
		$item = $this->formArray['campos'][$id];
		/* Options */
		$options = '';
		// id, name, length, type, value and extra
		$options .= (isset($item['id_show']) && !$item['id_show']) ? '' : "id=\"{$id}\" ";
		$options .= (!isset($item['array_group']) || $item['array_group']) ? "name=\"{$this->formNome}[{$item['name']}]\" " : "name=\"{$item['name']}\" ";
		$options .= ($item['type'] != 'textarea' && $item['type'] != 'select') ? "type=\"{$item['type']}\" " : '';
		$options .= (isset($item['length'][0]) && $item['type'] == 'textarea') ? "rows=\"{$item['length'][0]}\" " : '';
		$options .= (isset($item['length'][1]) && $item['type'] == 'textarea') ? "cols=\"{$item['length'][1]}\" " : '';
		$options .= (isset($item['length'][0]) && $item['type'] != 'textarea') ? "size=\"{$item['length'][0]}\" " : '';
		$options .= (isset($item['length'][1]) && $item['type'] != 'textarea') ? "maxlength=\"{$item['length'][1]}\" " : '';
		$options .= (isset($item['value']) && $item['type'] != 'textarea') ? "value=\"{$item['value']}\" " : '';
		$options .= (isset($item['extra'])) ? $item['extra'] . ' ' : '';
		// radio: checked		
		$options .= (isset($item['checked']) && $item['checked']) ? 'checked="checked" ' : '';
		// textarea: disable & readonly
		$options .= (isset($item['disabled']) && $item['disabled']) ? 'disabled="disabled" ' : '';
		$options .= (isset($item['readonly']) && $item['readonly']) ? 'readonly="readonly" ' : '';
		$options = trim($options);
		/* Let's build */
		if ($item['type'] == 'textarea')
		{
			// value
			$tmpValue = (isset($item['value'])) ? $item['value'] : '';
			$output = "<textarea {$options}>{$tmpValue}</textarea>";
		}
		else if ($item['type'] == 'select')
		{
			$selectOptions = '';
			foreach ($item['select'] as $option)
			{
				// title, value, selected
				$tmp = '';
				$tmp .= (isset($option['value'])) ? "value=\"{$option['value']}\" " : '';
				$tmp .= ($option['selected']) ? 'selected="selected" ' : '';
				$selectOptions .= '<option ' . trim($tmp) . ">{$option['title']}</option>";
			}
			$output = "<select {$options}>{$selectOptions}</select>";
		}
		else
		{
			$output = "<input {$options} />";
		}		
		return $output;
	}
	
	/**
	* Get a hidden input to identify the form. (put this at the end of the form)
	* 
	* @return string
	*/
	public function formularioID()
	{
		$code = '<input type="hidden" name="' . $this->formNome . '[internal_sent]" value="1">';
		return $code;
	}
	
	/**
	* Process the forms - validate and reload the values
	* 
	* @return int	0 - not sent, 1 - sent without errors, 2 - sent with errors
	*/
	public function processarFormulario()
	{		
		if (!$this->verificaSeFoiEnviado()) return false;
		$tmpValidation = 1;
		$sent = $_REQUEST[$this->formNome];
		// Thru all the campos
		foreach ($this->formArray['campos'] as $id => $item)
		{
			$this->formArray['campos'][$id]['processed'] = true;
			$this->formArray['campos'][$id]['processed_value'] = (isset($sent[$item['name']])) ? $sent[$item['name']] : null;
			// Reload the values
			switch ($item['type'])
			{
				case 'hidden':
					// do nothing
				break;
				case 'radio':
					if (!isset($item['reload']) || $item['reload'])
					{
						$this->formArray['campos'][$id]['checked'] = ($sent[$item['name']] == $item['value']) ? true : false;
					}
				break;				
				case 'checkbox':					
					if (!isset($item['reload']) || $item['reload'])
					{						
						$this->formArray['campos'][$id]['checked'] = (isset($sent[$item['name']]) && $sent[$item['name']] == 'on') ? true : false;
					}
				break;				
				case 'select':
					if (!isset($item['reload']) || $item['reload'])
					{
						foreach ($item['select'] as $optionKey => $option)
						{							
							$this->formArray['campos'][$id]['select'][$optionKey]['selected'] = ($sent[$item['name']] == $option['value']) ? true : false;
						}
					}
				break;
				case 'file':
					//
					//$this->formArray['campos'][$id]['processed_value']
					$this->formArray['campos'][$id]['processed_value'] = array(
						'name' => $_FILES[$this->formNome]['name'][$item['name']],
						'type' => $_FILES[$this->formNome]['type'][$item['name']],
						'tmp_name' => $_FILES[$this->formNome]['tmp_name'][$item['name']],
						'error' => $_FILES[$this->formNome]['error'][$item['name']],
						'size' => $_FILES[$this->formNome]['size'][$item['name']]
					);
					//$this->formArray['campos'][$id]['validation']
				break;
				default:
					$this->formArray['campos'][$id]['value'] = (!isset($item['reload']) || $item['reload']) ? $sent[$item['name']] : ((isset($item['value'])) ? $item['value'] : '');
				break;
			}			
			// Validation campos
			$this->formArray['campos'][$id]['validation'] = (isset($item['regx'])) ? $this->validation($sent[$item['name']], $item['regx']) : true;
			$tmpValidation = ($tmpValidation) ? $this->formArray['campos'][$id]['validation'] : 2;
			if ($item['type'] == 'file') 
			{
				$this->formArray['campos'][$id]['validation'] = empty($this->formArray['campos'][$id]['processed_value']['error']) ? true : false;
			}
		}
		return $tmpValidation;
	}
	
	/**
	* Check if the form have at least one error
	* 
	* @return bol
	*/
	public function temErro($id = false)
	{
		if (!$this->verificaSeFoiEnviado()) return false;
		if (empty($id))
		{
			foreach ($this->formArray['campos'] as $key => $value)
			{
				if (!$value['validation']) { return true; }
			}
		} else {
			return (!$this->formArray['campos'][$id]['validation']) ? true : false;
		}
	}
	
	/**
	* Check/get the error message for one field.
	* 
	* @param string $id
	* @return string
	*/
	public function pegarErro($id)
	{
		if (!$this->verificaSeFoiEnviado()) return false;
		return (!$this->formArray['campos'][$id]['validation']) ? $this->formArray['error_msg'][$id] : '';
	}
	
	/**
	* Force an error in a field.
	* 
	* @param string $id
	* @return bol
	*/
	public function setarErro($id)
	{
		if (!$this->verificaSeFoiEnviado()) return false;
		$this->formArray['campos'][$id]['validation'] = false;
		return true;
	}
	
	/**
	* Get the input value typed by user.
	* 
	* @param string $id
	* @return mixed
	*/
	public function campoValorEnviado($id)
	{
		return $this->formArray['campos'][$id]['processed_value'];
	}
	
	/**
	* Check if a field is checked or not (radio/checkbox type)
	* 
	* @param string $id
	* @return bol
	*/
	public function isChecked($id)
	{
		return (!isset($this->formArray['campos'][$id]['checked']) || !$this->formArray['campos'][$id]['checked']) ? false : true;
	}
	
	/**
	* Check if a field is disabled or not
	* 
	* @param string $id
	* @return bol
	*/
	public function isDisabled($id)
	{
		return (isset($this->formArray['campos'][$id]['disabled']) && $this->formArray['campos'][$id]['disabled']) ? true : false;
	}	
	
	/**
	* Regular Expression Validator
	* 
	* @param string $value
	* @param string $regx
	* @return bol
	*/
	private function validation($value, $regx)
	{
		switch ($regx)
		{			
			case 'nao_vazio':
				return (empty($value)) ? false : true;
			break;
			
			case 'email':
				return (preg_match('/(?:[a-z0-9\'\.\-_\+\|]++|&amp;)+@[a-z0-9\-]+\.(?:[a-z0-9\-]+\.)*[a-z]+/Uusmi', $value)) ? true : false;
			break;
			
			default:
				return (preg_match($regx, $value)) ? true : false;
			break;
		}
	}
	
	/**
	* Check if the form was sent.
	* 
	* @return bol
	*/
	private function verificaSeFoiEnviado()
	{
		return (empty($_REQUEST[$this->formNome]['internal_sent'])) ? false : true;
	}
}
?>