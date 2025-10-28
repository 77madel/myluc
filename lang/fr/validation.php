<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	'accepted' => ":attribute doit être accepté.",
	'accepted_if' => ":attribute doit être accepté lorsque :other vaut :value.",
	'active_url' => ":attribute doit être une URL valide.",
	'after' => ":attribute doit être une date postérieure à :date.",
	'after_or_equal' => ":attribute doit être une date postérieure ou égale à :date.",
	'alpha' => ":attribute ne doit contenir que des lettres.",
	'alpha_dash' => ":attribute ne doit contenir que des lettres, chiffres, tirets et underscores.",
	'alpha_num' => ":attribute ne doit contenir que des lettres et des chiffres.",
	'array' => ":attribute doit être un tableau.",
	'ascii' => ":attribute ne doit contenir que des caractères et symboles ASCII (1 octet).",
	'before' => ":attribute doit être une date antérieure à :date.",
	'before_or_equal' => ":attribute doit être une date antérieure ou égale à :date.",
	'between' => [
		'array' => ":attribute doit contenir entre :min et :max éléments.",
		'file' => ":attribute doit être compris entre :min et :max kilo-octets.",
		'numeric' => ":attribute doit être compris entre :min et :max.",
		'string' => ":attribute doit contenir entre :min et :max caractères.",
	],
	'boolean' => ":attribute doit être vrai ou faux.",
	'can' => ":attribute contient une valeur non autorisée.",
	'confirmed' => "La confirmation de :attribute ne correspond pas.",
	'contains' => ":attribute ne contient pas une valeur requise.",
	'current_password' => "Le mot de passe est incorrect.",
	'date' => ":attribute doit être une date valide.",
	'date_equals' => ":attribute doit être une date égale à :date.",
	'date_format' => ":attribute doit correspondre au format :format.",
	'decimal' => ":attribute doit avoir :decimal décimales.",
	'declined' => ":attribute doit être refusé.",
	'declined_if' => ":attribute doit être refusé lorsque :other vaut :value.",
	'different' => ":attribute et :other doivent être différents.",
	'digits' => ":attribute doit comporter :digits chiffres.",
	'digits_between' => ":attribute doit comporter entre :min et :max chiffres.",
	'dimensions' => ":attribute a des dimensions d’image invalides.",
	'distinct' => ":attribute contient une valeur en double.",
	'doesnt_end_with' => ":attribute ne doit pas se terminer par l’un des éléments suivants : :values.",
	'doesnt_start_with' => ":attribute ne doit pas commencer par l’un des éléments suivants : :values.",
	'email' => ":attribute doit être une adresse e-mail valide.",
	'ends_with' => ":attribute doit se terminer par l’un des éléments suivants : :values.",
	'enum' => "La valeur sélectionnée pour :attribute est invalide.",
	'exists' => "La valeur sélectionnée pour :attribute est invalide.",
	'extensions' => ":attribute doit avoir l’une des extensions suivantes : :values.",
	'file' => ":attribute doit être un fichier.",
	'filled' => ":attribute doit avoir une valeur.",
	'gt' => [
		'array' => ":attribute doit contenir plus de :value éléments.",
		'file' => ":attribute doit être supérieur à :value kilo-octets.",
		'numeric' => ":attribute doit être supérieur à :value.",
		'string' => ":attribute doit comporter plus de :value caractères.",
	],
	'gte' => [
		'array' => ":attribute doit contenir au moins :value éléments.",
		'file' => ":attribute doit être supérieur ou égal à :value kilo-octets.",
		'numeric' => ":attribute doit être supérieur ou égal à :value.",
		'string' => ":attribute doit comporter au moins :value caractères.",
	],
	'hex_color' => ":attribute doit être une couleur hexadécimale valide.",
	'image' => ":attribute doit être une image.",
	'in' => "La valeur sélectionnée pour :attribute est invalide.",
	'in_array' => ":attribute doit exister dans :other.",
	'integer' => ":attribute doit être un entier.",
	'ip' => ":attribute doit être une adresse IP valide.",
	'ipv4' => ":attribute doit être une adresse IPv4 valide.",
	'ipv6' => ":attribute doit être une adresse IPv6 valide.",
	'json' => ":attribute doit être une chaîne JSON valide.",
	'list' => ":attribute doit être une liste.",
	'lowercase' => ":attribute doit être en minuscules.",
	'lt' => [
		'array' => ":attribute doit contenir moins de :value éléments.",
		'file' => ":attribute doit être inférieur à :value kilo-octets.",
		'numeric' => ":attribute doit être inférieur à :value.",
		'string' => ":attribute doit comporter moins de :value caractères.",
	],
	'lte' => [
		'array' => ":attribute ne doit pas contenir plus de :value éléments.",
		'file' => ":attribute doit être inférieur ou égal à :value kilo-octets.",
		'numeric' => ":attribute doit être inférieur ou égal à :value.",
		'string' => ":attribute doit comporter au plus :value caractères.",
	],
	'mac_address' => ":attribute doit être une adresse MAC valide.",
	'max' => [
		'array' => ":attribute ne doit pas contenir plus de :max éléments.",
		'file' => ":attribute ne doit pas dépasser :max kilo-octets.",
		'numeric' => ":attribute ne doit pas être supérieur à :max.",
		'string' => ":attribute ne doit pas dépasser :max caractères.",
	],
	'max_digits' => ":attribute ne doit pas comporter plus de :max chiffres.",
	'mimes' => ":attribute doit être un fichier de type : :values.",
	'mimetypes' => ":attribute doit être un fichier de type : :values.",
	'min' => [
		'array' => ":attribute doit contenir au moins :min éléments.",
		'file' => ":attribute doit faire au moins :min kilo-octets.",
		'numeric' => ":attribute doit être au moins égal à :min.",
		'string' => ":attribute doit comporter au moins :min caractères.",
	],
	'min_digits' => ":attribute doit comporter au moins :min chiffres.",
	'missing' => ":attribute doit être manquant.",
	'missing_if' => ":attribute doit être manquant lorsque :other vaut :value.",
	'missing_unless' => ":attribute doit être manquant sauf si :other vaut :value.",
	'missing_with' => ":attribute doit être manquant lorsque :values est présent.",
	'missing_with_all' => ":attribute doit être manquant lorsque :values sont présents.",
	'multiple_of' => ":attribute doit être un multiple de :value.",
	'not_in' => "La valeur sélectionnée pour :attribute est invalide.",
	'not_regex' => "Le format de :attribute est invalide.",
	'numeric' => ":attribute doit être un nombre.",
	'password' => [
		'letters' => ":attribute doit contenir au moins une lettre.",
		'mixed' => ":attribute doit contenir au moins une majuscule et une minuscule.",
		'numbers' => ":attribute doit contenir au moins un chiffre.",
		'symbols' => ":attribute doit contenir au moins un symbole.",
		'uncompromised' => "La valeur de :attribute a été compromise. Veuillez choisir un autre :attribute.",
	],
	'present' => ":attribute doit être présent.",
	'present_if' => ":attribute doit être présent lorsque :other vaut :value.",
	'present_unless' => ":attribute doit être présent sauf si :other vaut :value.",
	'present_with' => ":attribute doit être présent lorsque :values est présent.",
	'present_with_all' => ":attribute doit être présent lorsque :values sont présents.",
	'prohibited' => ":attribute est interdit.",
	'prohibited_if' => ":attribute est interdit lorsque :other vaut :value.",
	'prohibited_unless' => ":attribute est interdit sauf si :other est dans :values.",
	'prohibits' => ":attribute empêche :other d’être présent.",
	'regex' => "Le format de :attribute est invalide.",
	'required' => ":attribute est requis.",
	'required_array_keys' => ":attribute doit contenir des entrées pour : :values.",
	'required_if' => ":attribute est requis lorsque :other vaut :value.",
	'required_if_accepted' => ":attribute est requis lorsque :other est accepté.",
	'required_if_declined' => ":attribute est requis lorsque :other est refusé.",
	'required_unless' => ":attribute est requis sauf si :other est dans :values.",
	'required_with' => ":attribute est requis lorsque :values est présent.",
	'required_with_all' => ":attribute est requis lorsque :values sont présents.",
	'required_without' => ":attribute est requis lorsque :values n’est pas présent.",
	'required_without_all' => ":attribute est requis lorsque aucun des éléments :values n’est présent.",
	'same' => ":attribute doit correspondre à :other.",
	'size' => [
		'array' => ":attribute doit contenir :size éléments.",
		'file' => ":attribute doit faire :size kilo-octets.",
		'numeric' => ":attribute doit être :size.",
		'string' => ":attribute doit comporter :size caractères.",
	],
	'starts_with' => ":attribute doit commencer par l’un des éléments suivants : :values.",
	'string' => ":attribute doit être une chaîne de caractères.",
	'timezone' => ":attribute doit être un fuseau horaire valide.",
	'unique' => ":attribute a déjà été pris.",
	'uploaded' => "Le téléversement de :attribute a échoué.",
	'uppercase' => ":attribute doit être en majuscules.",
	'url' => ":attribute doit être une URL valide.",
	'ulid' => ":attribute doit être un ULID valide.",
	'uuid' => ":attribute doit être un UUID valide.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap our attribute placeholder
	| with something more reader friendly such as "E-Mail Address" instead
	| of "email". This simply helps us make our message more expressive.
	|
	*/

	'attributes' => [],

];


