
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

	//	...
	$OP.i18n.getLanguageCode = function(){
		//	By URL Query.
		var lang = $OP.URL.Query.Get('lang');

		//	By Web Strage.
		if(!lang ){
			lang = localStorage.getItem('$OP.i18n.language.code');
		}

		//	By Browser.
		if(!lang ){
			lang = (navigator.browserLanguage || navigator.language || navigator.userLanguage);
		}

		//	...
		return lang;
	};

	//	...
	document.addEventListener('DOMContentLoaded', function(){
		//	...
		var from = $OP.i18n.AppLanguageCode();
		var to   = $OP.i18n.SelectedLanguageCode("<?= $_GET['locale']['to'] ?? null ?>");

		//	...
		if(!to ){
			return;
		}

		//	...
		$OP.i18n.Translate(from, to);
	});
})();
