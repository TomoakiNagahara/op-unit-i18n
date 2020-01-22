
/**
 * app-webpack-js:/i18n.js
 *
 * @created   2017-06-07
 * @version   1.0
 * @package   app-skeleton
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
//	...
(function(){
	//	...
	if(!$OP ){
		return;
	};

	/** local storage key name.
	 *
	 * @created   2020-01-20
	 * @param     string
	 */
	const _LANGUAGE_CODE_ = 'op_unit_i18n_selected_language_code';

	//	...
	$OP.i18n = {};

	/** Get the host name of server, used for translation.
	 *
	 * @created   2020-01-20
	 * @return    string
	 */
	$OP.i18n.Hostname = function(){
		return '<?php echo $_SERVER["HTTP_HOST"] ?>';
	};

	/** Get the URL of server, used for translation.
	 *
	 * @created   2020-01-20
	 * @return    string
	 */
	$OP.i18n.URL = function(){
		return '<?php echo \OP\UNIT\App::URL("app:/api/i18n/") ?>';
	};

	//	...
	$OP.i18n.Translate = function(from, to){
		//	Check of args.
		if( !to && !from ){
			D('Did not pass args of "from" and "to" locale.');
			return;
		}

		//	Init.
		var i    = 0;
		var dom  = null;
		var doms = [];
		var post = {};
			post.target= 'translate';
			post.to    = to;
			post.from  = from;
			post.strings = [];

		//	...
		while( dom = document.querySelector('[data-i18n="true"]') ){
			//	...
			i++;

			//	...
			if( i > 200 ){
				//	...
				break;
			};

			//	...
			dom.dataset.i18n = 'false';

			//	...
			var locale = dom.dataset.i18nLocale;
			var string = dom.innerHTML;

			//	Check dom has locale.
			if(!locale ){
				D('Attribute "data-i18n-locale" has not been set this node.', dom);
				continue;
			}

			//	Get each language code.
			var regexp    = /^[a-z]+/i;
			var lang_dom  = locale.match(    regexp )[0];
			var lang_from = post.from.match( regexp )[0];
			var lang_to   = post.to.match(   regexp )[0];

			//	Check source language is unmatch.
			if( lang_dom !== lang_from ){
				D(lang_dom +'!=='+ lang_from);
				continue ;
			}

			//	Check translate language is match.
			if( lang_dom === lang_to ){
				D(lang_dom +'==='+ lang_from);
				continue ;
			}

			//	...
			doms.push(dom);
			post.strings.push(string);
		};

		//	...
		var url  = '//' + $OP.i18n.Hostname() + $OP.i18n.URL('translation');

		//	...
		if( post.strings.length === 0 ){
			return;
		};

		//	...
		$OP.Ajax.Post(url, post, function(json){
			//	...
			var result = json.result.translate;
			var len    = result ? result.length: 0;

			//	Repalace each translate result.
			for(var i=0; i<len; i++ ){
				//	Save disable node.
				var origin_nodes = doms[i].querySelectorAll('[data-i18n=false]');

				//	Replace translate result string.
				if( doms[i].innerHTML ){
					doms[i].innerHTML = result[i];
				}else
				if( doms[i].innerHtml ){
					doms[i].innerHtml = result[i];
				}else{
					D("Has not been set innerHtml.");
				};

				//	Overwrite data-i18n-locale value.
				doms[i].dataset.i18nLocale = post.to;

				//	Extraction "data-i18n=false".
				var nodes = doms[i].querySelectorAll('[data-i18n=false]');

				//	Unmatch node length
				if( origin_nodes.length !== nodes.length ){
					D(origin_nodes, nodes);
					continue;
				};

				//	Replace each node.
				for(let i=0; i<origin_nodes.length; i++){
					if( nodes[i].innerHTML ){
						nodes[i].innerHTML = origin_nodes[i].innerHTML;
					}else
					if( nodes[i].innerHtml ){
						nodes[i].innerHtml = origin_nodes[i].innerHtml;
					};
				};
			};
		}, function(status){
			//	...
			D('status', status);
		});
	};

	/** Set language code to local storage.
	 *
	 * @param string language_code
	 */
	$OP.i18n.SetLanguageCode = function(lang){
		localStorage.setItem(_LANGUAGE_CODE_, lang);
	}

	/** Get translate language code from URL-Query, local storage, browser.
	 *
	 * @return string language_code
	 */
	$OP.i18n.GetLanguageCode = function(){
		//	By URL Query.
		var lang = $OP.URL.Query.Get('lang');

		//	By Web Strage.
		if(!lang ){
			lang = localStorage.getItem(_LANGUAGE_CODE_);
		}

		//	By Browser.
		if(!lang ){
			lang = (navigator.browserLanguage || navigator.language || navigator.userLanguage);
		}

		//	...
		return lang;
	};

	/** Get app language code.
	 *
	 * @created   2020-01-21
	 * @return    string       application_language_code
	 */
	$OP.i18n.AppLanguageCode = function(){
		return "<?= $_GET['locale']['from'] ?? \OP\Env::Locale() ?>";
	}

	/** Get language code list and display html.
	 *
	 * @created   2020-01-20
	 * @changed   2020-01-22   Separate to DisplayLanguageCode().
	 * @param     string       locale
	 * @param     function     success
	 * @return    object       json
	 */
	$OP.i18n.GetLanguageList = function(locale, success){
		//	Init.
		var url  = '//' + $OP.i18n.Hostname() + $OP.i18n.URL('language');
		var post = {};
			post.target = 'language';
			post.locale =  locale;

		//	Fetch laguage code list.
		$OP.Ajax.Post(url, post, function(json){

			//	Execute success function.
			success(json.result.language);

		}, function(status){
			//	...
			D('status', status);
		});
	};

	/** Display language code list.
	 *
	 * @created   2020-01-22
	 * @param     array        list
	 */
	$OP.i18n.DisplayLanguageList = function(result){

		//	Get laguage list area.
		var div = document.querySelector('#unit-i18n-language .language-area');
			div.innerText = '';

		//	Rendering html.
		for(var lang of result ){
			var code = lang.code;
			var name = lang.name;
			var span = document.createElement('span');
				span.innerText = name;
				span.classList = 'language';
				span.dataset.i18nLanguage = code;
				div .appendChild(span);

			//	Set click event to language name.
			span.addEventListener('click',function(){
				//	Current language code.
				var current  = localStorage.getItem(_LANGUAGE_CODE_);
				if(!current ){
					current = $OP.i18n.AppLanguageCode();
				};

				//	Set selected language code.
				var language = this.dataset.i18nLanguage;
				$OP.i18n.GetLanguageCode(language);

				//	Redo get language list.
				$OP.i18n.GetLanguageList(language, true);

				//	Reset i18n value.
				while( dom = document.querySelector('[data-i18n="false"]') ){
					dom.dataset.i18n = 'true';
				};

				//	Translate.
				$OP.i18n.Translate(current, language);
			});
		};
	};

	/** Do auto translate and auto display language list.
	 *
	 *  Just only load.
	 */
	document.addEventListener('DOMContentLoaded', function(){
		_auto_translate();
		_auto_language_list();
	});

	/** Auto translate.
	 *
	 * @created  2020-01-22
	 */
	function _auto_translate(){
		//	Get language code.
		var from = $OP.i18n.AppLanguageCode();
		var to   = $OP.i18n.GetLanguageCode();

		//	...
		if(!to ){
			return;
		}

		//	Translate.
		$OP.i18n.Translate(from, to);
	}

	/** Auto display language list.
	 *
	 * @created  2020-01-22
	 */
	function _auto_language_list(){
		//	Check init object.
		if(!$OP.i18n || !$OP.i18n.GetLanguageList ){
			return;
		}

		//	Check if already language code selected.
		var language = localStorage.getItem(_LANGUAGE_CODE_);

		//	If language code is not selected yet.
		if(!language ){
			//	Open language code selector list.
			_open_language_list();
		}

		//	Get language code list show/hide button.
		var button = document.querySelector('#unit-i18n-language .language-toggle');
		if(!button ){
			return;
		}

		//	Add show/hide button.
		var show = button.dataset.i18nLanguageIconShow;
		var hide = button.dataset.i18nLanguageIconHide;
		button.innerText = language ? show: hide;

		//	Add event lister to language code list open/close button.
		button.addEventListener('click', function(event){
			var area   = document.querySelector('#unit-i18n-language .language-area');
			var target = event.target;
			var current= target.innerText;
			var show   = target.dataset.i18nLanguageIconShow;
			var hide   = target.dataset.i18nLanguageIconHide;

			//	...
			if( current === show ){
				//	Do hide.
				target.innerText = hide;
				area.classList.remove('hide');
			}else{
				//	Do show.
				target.innerText = show;
				area.classList.add('hide');
			}
		});
	}
})();
