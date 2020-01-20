
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

	/** Get language code list and display html.
	 *
	 * @created   2020-01-20
	 * @param     string       locale
	 * @param     boolean      is_display_html
	 * @return    object       json
	 */
	$OP.i18n.Language = function(locale, is_display_html){
		//	...
		var url  = '//' + $OP.i18n.Hostname() + $OP.i18n.URL('language');
		var post = {};
			post.target = 'language';
			post.locale =  locale;

		//	...
		$OP.Ajax.Post(url, post, function(json){
			//	...
			D('$OP.i18n.Language',json);
		}, function(status){
			//	...
			D('status', status);
		});
	};

	//	...
	$OP.i18n.Translate = function(){
		var i    = 0;
		var to   = null;
		var from = null;
		var dom  = null;
		var doms = [];
		var post = {};
			post.to    = '<?= \OP\Env::Locale() ?>';
			post.from  = null;
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
			var locale = dom.dataset.locale;
			var string = dom.innerHTML;

			//	...
			if( post.from === null ){
				post.from = locale;
			};

			//	...
			if(!locale ){
				D('No locale has been set this node.', dom);
				continue;
			}else if( locale !== post.from ){
				D(`Unmatch locale. (${locale})`);
				continue;
			}else{
			//	D(locale, string);
			};

			//	...
			doms.push(dom);
			post.strings.push(string);
		};

		//	...
		var url = 'app:/api/i18n/';

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
				}else{
				if( doms[i].innerHtml ){
					doms[i].innerHtml = result[i];
				}else
					D("Has not been set innerHtml.");
				};

				//	Extraction "data-i18n=false".
				var nodes = doms[i].querySelectorAll('[data-i18n=false]');

				//	Unmatch node length
				if( origin_nodes.length !== nodes.length ){
					D(origin_nodes, nodes);
					continue;
				};

				//	Replace each node.
				for(let i=0; i<origin_nodes.length; i++){
					nodes[i].innerText = origin_nodes[i].innerText;
				};
			};
		}, function(status){
			//	...
			D(status);
		});
	};

	//	...
	$OP.i18n.setLanguageCode = function(lang){
		localStorage.setItem('$OP.i18n.language.code', lang);
	};

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
		$OP.i18n.Translate();
	});
})();
