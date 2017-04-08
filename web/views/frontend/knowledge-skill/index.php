<?php
use yii\helpers\Html;
use app\models\BusinessAssets;
use app\models\Project;
use app\models\Config;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '技能管理';
$this->params['breadcrumbs'][] = $this->title;

AppAsset::register($this);
$this->registerCssFile('@web/css/lib/select2.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/lib/jstree/jstree.css',['depends'=>['app\assets\AppAsset']]);
$this->registerCssFile('@web/css/knowledge-skill/index.css',['depends'=>['app\assets\AppAsset']]);

$this->registerJsFile('@web/js/lib/select2.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/jstree.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/d3.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
$this->registerJsFile('@web/js/lib/knockout.min.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);
//$this->registerJsFile('@web/js/lib/skilltree/skilltree.js',['depends'=>['app\assets\AppAsset'], 'position'=>$this::POS_HEAD]);

?>
<style type="text/css">
    .area-tree {
        padding-left:0px;
        padding-right:0px;
    }
</style>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <div class="col-md-4 panel panel-default skill-list" style="height:1024px;" >
    </div>
    <div id="skill_tree" class="col-md-8 panel panel-default" style=' height:1024px; '>
        <div data-bind="css: { open: isOpen }" class="page">
            <div class="col-md-2" >
                <?php foreach ($type_dict_arr as $index => $name) {
                ?>
                    <p class="skill_type_nav" ><?php echo $name;?></p>
                <?php
                }
                ?>
            </div>
            <div class="col-md-10 talent-tree" >
                <h2 class="start-helper" data-bind="css:{active:noPointsSpent}">从这开始!</h2>
                <!--ko foreach: skills-->
                    <div data-bind="css: { 'can-add-points': canAddPoints, 'has-points': hasPoints, 'has-max-points': hasMaxPoints }, attr: { 'data-skill-id': id }, style: { 'margin-left': margin_left, 'margin-top': margin_top}" class="skill" >
                        <div class="icon-container">
                            <div class="icon"></div>
                        </div>
                        <div class="frame">
                            <div class="tool-tip">
                                <h3 data-bind="text: title" class="skill-name"></h3>
                                <div data-bind="html: description" class="skill-description"></div>
                                <ul class="skill-links">
                                    <!--ko foreach: links-->
                                    <li>
                                        <a data-bind="attr: { href: url }, click: function(){ 
                                            _gaq.push(['_trackEvent',$parent.title, label, url]);
                                            return true;
                                            }, text: label" target="_blank"></a>
                                    </li>
                                    <!--/ko-->
                                </ul>
                                <hr data-bind="visible: currentRankDescription() || nextRankDescription()">
                                <div data-bind="if: currentRankDescription" class="current-rank-description">当前级别: <span data-bind="    text: currentRankDescription"></span></div>
                                <div data-bind="if: nextRankDescription" class="next-rank-description">下一级别: <span data-bind=" text: nextRankDescription"></span></div>
                                <hr>
                                <ul class="stats">
                                    <!--ko foreach: stats-->
                                    <li><span class="value">+<span data-bind="text: value"></span></span> <span data-bind=" text: title" class="title"></span></li>
                                    <!--/ko-->
                                </ul>
                                <!--ko if: talentSummary-->
                                <div class="talent-summary">获得天赋: <span data-bind="text: talentSummary"></span></div>
                                <!--/ko-->
                                <div data-bind="text: helpMessage" class="help-message"></div>
                            </div>
                            <div class="skill-points"><span data-bind="text: points" class="points"></span>/<span data-bind="   text: maxPoints" class="max-points"></span></div>
                            <div data-bind="click: addPoint, rightClick: removePoint" class="hit-area"></div>
                        </div>
                    </div>
                <!--/ko-->
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
var add_user_skill_href = "/frontend/knowledge-api/add-user-skill";
var remove_user_skill_href = "/frontend/knowledge-api/remove-user-skill";
(function($, ko){
	//Private utilities
	function namespace(namespaceString) {
		var parts = namespaceString.split('.'),
			parent = window,
			currentPart = '';

		for(var i = 0, length = parts.length; i < length; i++) {
			currentPart = parts[i];
			parent[currentPart] = parent[currentPart] || {};
			parent = parent[currentPart];
		}

		return parent;
	}
	function prettyJoin(array) {
		if(array.length >2) array = [array.slice(0,array.length-1).join(', '), array[array.length-1]];
		return array.join(' and ');
	}
	// A deep cloner that only works for simple objects.
	// Invalid for cloning objects with functions or classes (including Date).
	function simpleDeepClone(src) {
		if (!src) { return src; }
		return JSON.parse(JSON.stringify(src));
	}

	//Custom binding handlers
	ko.bindingHandlers.rightClick = {
		init: function(element, valueAccessor) {
			$(element).on('mousedown', function(event) {
				if(event.which==3) valueAccessor()();
			}).on('contextmenu', function(event) {
				event.preventDefault();
			});
		}
	};

	//tft.skilltree namespace
	(function(ns) {

		//VM for the entire UI
		var Calculator = ns.Calculator = function(_e){
			var e = _e || {};
			var self = function(){};

			//constants for hash generation
			var asciiOffset = 96; //64 for caps, 96 for lower
			var hashDelimeter = '_';

			var numPortraits = e.numPortraits || 1;

			//Intro vs Talent Tree UI state
			self.isOpen = ko.observable(true);
			self.open = function() {
				self.isOpen(true);
			};
			self.close = function() {
				self.isOpen(false);
			};
			self.toggle = function() {
				self.isOpen(!self.isOpen());
			};

			//Mega skill list population
			self.skills = ko.observableArray(ko.utils.arrayMap(e.skills, function(item){
				return new Skill(item, self.skills, e.learnTemplate);
			}));
			function getSkillById(id) {
				return ko.utils.arrayFirst(self.skills(), function(item){
					return item.id == id;
				});
			}
			//Wire up dependency references
			ko.utils.arrayForEach(e.skills, function(item){
				if(item.dependsOn) {
					var dependent = getSkillById(item.id);
					ko.utils.arrayForEach(item.dependsOn, function(dependencyId){
						var dependency = getSkillById(dependencyId);
						dependent.dependencies.push(dependency);
						dependency.dependents.push(dependent);
					});
				}
			});

			//Avatar properties
			self.avatarName = ko.observable('你的名字');
			//level = total of all points spent
			self.level = ko.computed(function(){
				var totalSkillPoints = 0;
				ko.utils.arrayForEach(self.skills(), function(skill){
					totalSkillPoints += skill.points();
				});
				return totalSkillPoints + 1;
			});
			self.noPointsSpent = ko.computed(function(){
				return !Boolean(ko.utils.arrayFirst(self.skills(), function(skill){
					return (skill.points() > 0);
				}));
			});
			self.stats = ko.computed(function(){
				//set some defaults
				var totals = simpleDeepClone(e.defaultStats) || {};
				//get all the skill name/value pairs and add/create them, using the stat name as the index
				ko.utils.arrayForEach(self.skills(), function(skill){
					var p = skill.points();
					if(p>0) ko.utils.arrayForEach(skill.stats, function(stat){
						var total = totals[stat.title] || 0;
						total += stat.value * p; //multiply the stat value by the points spent on the skill
						totals[stat.title] = total;
					});
				});
				//Translate into a view-friendly array
				var result = [];
				for(var statName in totals) {
					result.push({
						title:statName
						, value:totals[statName]
					});
				}
				return result;
			});
			//String of unique characteristics, comma delimeted
			self.talentSummary = ko.computed(function(){
				var a = [];
				ko.utils.arrayForEach(self.skills(), function(skill){
					if(skill.hasPoints()) a = a.concat(skill.talents)
				});
				return a.join(', ');
			});
			//Portrait stuff
			self.portrait = ko.observable(Math.ceil(Math.random() * numPortraits));
			self.portraitURL = ko.computed(function(){
				return (e.portraitPathTemplate || 'img/portraits/portrait-{n}.jpg').replace('{n}', self.portrait());
			});
			self.choosePreviousPortrait = function(){
				var n = self.portrait() - 1;
				if(n<1) n = numPortraits;
				self.portrait(n);
			};
			self.chooseNextPortrait = function(){
				var n = self.portrait() + 1;
				if(n>numPortraits) n = 1;
				self.portrait(n);
			};

			//Utility functions
			self.newbMode = function(){
				ko.utils.arrayForEach(self.skills(), function(skill){
					skill.points(0);
				});
			};
			self.godMode = function(){
				ko.utils.arrayForEach(self.skills(), function(skill){
					skill.points(skill.maxPoints);
				});
			};

			//Hash functions
			self.hash = ko.computed(function(){
				var a = [];
				//compile a flat list of skill ids and values
				ko.utils.arrayForEach(self.skills(), function(skill){
					if(skill.hasPoints()) {
						a.push(String.fromCharCode(skill.id + asciiOffset)); //convert skill id to letter of the alphabet
						if(skill.hasMultiplePoints()) a.push(skill.points()); //only include points if they are > 1
					}
				});
				return ['', a.join(''), self.portrait(), self.avatarName()].join(hashDelimeter);
			});
			//Update the skill tree based on a new hash
			function useHash(hash) {
				if(hash) {
					doUpdateHash = false;
					self.newbMode();

					var hashParts = hash.split(hashDelimeter);
					if(hashParts[2]) self.portrait(Number(hashParts[2])); //use the segment after the second delimeter as the portrait index
					if(hashParts[3]) self.avatarName(hashParts[3]); //use the segment after the third delimeter as the avatar name

					var s = hashParts[1]; //use the segment after the first delimeter as the skill hash

					var pairs = [];

					//break the hash back down into skill/value pairs, one character at a time
					var hashCharacters = s.split('');
					for(var i=0; i<hashCharacters.length; i++) {
						if(!Number(hashCharacters[i])) { //if the current character is not a number,
							var skill = getSkillById(hashCharacters[i].charCodeAt(0)-asciiOffset) //convert the character to a skill id and look it up
							if(skill) {
								var points = Number(hashCharacters[i+1]) || 1; //default to 1 point if the number is not specified next
								pairs.push({
									skill: skill
									, points: points
								})
							}
						}
					}

					//cycle through the whole list, adding points where possible, until the list is depleted
					var pointsWereAllocated = true; //flag
					while(pointsWereAllocated) {
						pointsWereAllocated = false; //assume the list is depleted by default
						ko.utils.arrayForEach(pairs, function(pair){
							if(!pair.wasAllocated && pair.skill.canAddPoints()) { //only add points once, and only where possible
								pair.skill.points(Math.min(pair.skill.maxPoints, pair.points)); //don't add more points than allowed
								pair.wasAllocated = true; //don't add this one again
								pointsWereAllocated = true;
							}
						});
					}

					doUpdateHash = true;
				}
			};

			//Hash throttling

			//update the address bar when the hash changes
			function useLastHash() {
				useHash(lastHash);
			}
			function updateHash(s) {
				window.location.hash = s || newHash;
			}
			var lastHash, useHash_timeout, newHash, updateHash_timeout, doUpdateHash = true;
			self.useHash = function(hash) {
				lastHash = hash;
				clearTimeout(useHash_timeout);
				useHash_timeout = setTimeout(useLastHash, 50);
			}
			self.hash.subscribe(function(newValue){
				if(doUpdateHash) {
					newHash = newValue;
					clearTimeout(updateHash_timeout);
					updateHash_timeout = setTimeout(updateHash, 50);
				}
			});

			window.onhashchange = function () {
				self.useHash(decodeURI(window.location.hash.substr(1)));
			};

			//Launch
            //用hash控制页面数据缓存
			/* var currentHash = decodeURI(window.location.hash.substr(1)); */
			/* self.isOpen(currentHash != ''); //If there is a hash, open the skill tree by default */
			/* self.useHash(currentHash); */

			return self;
		}
		//VM for individual skills
		var Skill = ns.Skill = function(_e, allSkills, learnTemplate){
			var e = _e || {};
			var self = function(){};

			//Basic properties
			self.id = e.id || 0;
			self.title = e.title || '未知技能';
			self.description = e.description;
			self.margin_left = e.margin_left;
			self.margin_top = e.margin_top;
			self.maxPoints = e.maxPoints || 1;
			self.points = ko.observable(e.points || 0);
			self.links = ko.utils.arrayMap(e.links, function(item){
				return new Link(item);
			});
			self.dependencies = ko.observableArray([]);
			self.dependents = ko.observableArray([]);
			self.stats = e.stats || [];
			self.rankDescriptions = e.rankDescriptions || [];
			self.talents = e.talents || [];

			//Computed values
			self.hasDependencies = ko.computed(function(){
				return self.dependencies().length > 0;
			});
			self.dependenciesFulfilled = ko.computed(function(){
				var result = true;
				ko.utils.arrayForEach(self.dependencies(), function(item) {
					if(!item.hasPoints()) result = false;
				});
				return result;
			});
			self.dependentsUsed = ko.computed(function(){
				var result = false;
				ko.utils.arrayForEach(self.dependents(), function(item) {
					if(item.hasPoints()) result = true;
				});
				return result;
			});
			self.hasPoints = ko.computed(function(){
				return self.points() > 0;
			});
			self.hasMultiplePoints = ko.computed(function(){
				return self.points() > 1;
			});
			self.hasMaxPoints = ko.computed(function(){
				return self.points() >= self.maxPoints;
			});
			self.canAddPoints = ko.computed(function(){
				return self.dependenciesFulfilled() && !self.hasMaxPoints();
			});
			self.canRemovePoints = ko.computed(function(){
				//Only allow points to be removed if:
				//	(A) There are dependents being used but more than one point spent here OR
				//	(B) There are NO dependents being used and any number of points spent here
				return (self.dependentsUsed() && self.hasMultiplePoints()) || (!self.dependentsUsed() && self.hasPoints());
			});
			//Summarize what the user needs to unlock this skill (if anything)
			self.helpMessage = ko.computed(function(){
				if(!self.dependenciesFulfilled()){
					var s = [];
					ko.utils.arrayForEach(self.dependencies(), function(item) {
						if(!item.hasMaxPoints()) s.push(item.title);
					});
					return (learnTemplate || '学习 {n} 进行解锁.').replace('{n}', prettyJoin(s));
				}
				return '';
			});
			self.talentSummary = ko.computed(function(){
				return self.talents.join(', ');
			});
			self.currentRankDescription = ko.computed(function(){
				return self.rankDescriptions[self.points()-1];
			});
			self.nextRankDescription = ko.computed(function(){
				return self.rankDescriptions[self.points()];
			});

			//Methods
			self.addPoint = function() {
                var href = add_user_skill_href;
                var post_data = {
                    'skill_id' : self.id,
                    'level' : self.points() + 1,
                };
                var add_point_func = function (data) {
				    if(self.canAddPoints()) self.points(self.points() + 1);
                }  
                directPost(href, post_data, true, true, add_point_func);
			}
			self.removePoint = function() {
                var href = remove_user_skill_href;
                var post_data = {
                    'skill_id' : self.id,
                    'level' : self.points(),
                };
                var remove_point_func = function (data) {
				    if(self.canRemovePoints()) self.points(self.points() - 1);
                }  
                directPost(href, post_data, true, true, remove_point_func);
			}

			return self;
		}
		//VM for a simple hyperlink
		var Link = ns.Link = function(_e){
			var e = _e || {};
			var self = function(){};

			//Basic properties
			self.label = e.label || (e.url || 'Learn more');
			self.url = e.url || 'javascript:void(0)';

			return self;
		}
	})(namespace('tft.skilltree'));

})(window.jQuery, window.ko);

var skill_list = <?php echo $skill_list;?>;
//konami code plugin
(function ($) {

    $.fn.konami = function (callback, code) {
        if (code == undefined) code = "38,38,40,40,37,39,37,39,66,65"; //Super secret!

        return this.each(function () {
            var kkeys = [];
            $(this).keydown(function (e) {
                kkeys.push(e.keyCode);
                if (kkeys.toString().indexOf(code) >= 0) {
                    $(this).unbind('keydown', arguments.callee);
                    callback(e);
                }
            });
        });
    }

})(jQuery);

//Custom closure
(function($, ko, data){

    //IE checks
    function getInternetExplorerVersion() {
        var rv = -1; // Return value assumes failure.
        if (navigator.appName == 'Microsoft Internet Explorer') {
            var ua = navigator.userAgent;
            var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
            if (re.exec(ua) != null)
                rv = parseFloat(RegExp.$1);
        }
        return rv;
    }
    function isInvalidIEVersion() {
        var ver = getInternetExplorerVersion();
        if (ver > -1 && ver < 9) {
            $('html').addClass("ltIE9");
            return true;
        }
        return false;
    }

    //On page load
    $(function(){

        //Quit if using an IE we don't like
        if (isInvalidIEVersion()) return;

        //Create and bind the viewmodel
        var vm = new tft.skilltree.Calculator(data);
        ko.applyBindings(vm);

        //apply konami code plugin
        $(window).konami(function () { vm.open(); vm.godMode(); });

        //Allow a split second for binding before turning on animated transitions for the UI
        setTimeout(function(){
            $('.page').addClass('animated');
        }, 50);
    });


})(window.jQuery, window.ko, {
    learnTemplate: '学习 {n} 进行解锁.',
    portraitPathTemplate: 'img/portraits/portrait-{n}.jpg', 
    numPortraits: 22, 
    defaultStats: {
        'Charisma': 9
        , 'Dexterity': 9
        , 'Fortitude': 9
        , 'Intellect': 9
        , 'Strength': 9
        , 'Wisdom': 9
    },
    skills : skill_list
});
</script>
