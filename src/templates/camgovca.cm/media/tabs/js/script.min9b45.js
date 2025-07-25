/*
 * Copyright © 2016 NoNumber - All Rights Reserved
 * License http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */var nn_tabs_urlscroll=0;var nn_tabs_use_hash=nn_tabs_use_hash||0;var nn_tabs_reload_iframes=nn_tabs_reload_iframes||0;var nn_tabs_init_timeout=nn_tabs_init_timeout||0;var nnTabs=null;(function($){"use strict";$(document).ready(function(){if(typeof(window['nn_tabs_use_hash'])!="undefined"){setTimeout(function(){nnTabs.init();},nn_tabs_init_timeout);}});nnTabs={timers:[],init:function(){var self=this;try{this.hash_id=decodeURIComponent(window.location.hash.replace('#',''));}catch(err){this.hash_id='';}
this.current_url=window.location.href;if(this.current_url.indexOf('#')!==-1){this.current_url=this.current_url.substr(0,this.current_url.indexOf('#'));}
$('.nn_tabs').removeClass('has_effects');this.showByURL();this.showByHash();this.initEqualHeights();setTimeout((function(){self.initActiveClasses();self.initClickMode();if(nn_tabs_use_hash){self.initHashHandling();}
self.initHashLinkList();if(nn_tabs_reload_iframes){self.initIframeReloading();}$('.nn_tabs').addClass('has_effects');}),1000);},show:function(id,scroll,openparents,slideshow){if(openparents){this.openParents(id,scroll);return;}
var self=this;var $el=this.getElement(id);if(!$el.length){return;}$el.tab('show');$el.closest('ul.nav-tabs').find('.nn_tabs-toggle').attr('aria-selected',false);$el.attr('aria-selected',true);$el.closest('div.nn_tabs').find('.tab-content').first().children().attr('aria-hidden',true);$('div#'+id).attr('aria-hidden',false);this.updateActiveClassesOnTabLinks($el);if(!slideshow){$el.focus();}},getElement:function(id){return this.getTabElement(id);},getTabElement:function(id){return $('a.nn_tabs-toggle[data-id="'+id+'"]');},getSliderElement:function(id){return $('#'+id+'.nn_sliders-body');},showByURL:function(){var id=this.getUrlVar();if(id==''){return;}
this.showByID(id);},showByHash:function(){if(this.hash_id==''){return;}
var id=this.hash_id;if(id==''||id.indexOf("&")!=-1||id.indexOf("=")!=-1){return;}
if($('a.nn_tabs-toggle[data-id="'+id+'"]').length==0){this.showByHashAnchor(id);return;}
if(!nn_tabs_use_hash){return;}
if(!nn_tabs_urlscroll){$('html,body').animate({scrollTop:0});}
this.showByID(id);},showByHashAnchor:function(id){if(id==''){return;}
var $anchor=$('a#anchor-'+id);if($anchor.length==0){$anchor=$('a#'+id);}
if($anchor.length==0){return;}
if($anchor.closest('.nn_tabs').length==0){return;}
var $tab=$anchor.closest('.tab-pane').first();if($tab.find('.nn_sliders').length>0){return;}
this.openParents($tab.attr('id'),false);setTimeout(function(){$('html,body').animate({scrollTop:$anchor.offset().top});},250);},showByID:function(id){var $el=$('a.nn_tabs-toggle[data-id="'+id+'"]');if($el.length==0){return;}
this.openParents(id,nn_tabs_urlscroll);},openParents:function(id,scroll){var $el=this.getElement(id);if(!$el.length){return;}
var parents=[];var parent=this.getElementArray($el);while(parent){parents[parents.length]=parent;parent=this.getParent(parent.el);}
if(!parents.length){return false;}
this.stepThroughParents(parents,null,scroll);},stepThroughParents:function(parents,parent,scroll){var self=this;if(!parents.length&&parent){parent.el.focus();return;}
parent=parents.pop();if(parent.el.hasClass('in')||parent.el.parent().hasClass('active')){self.stepThroughParents(parents,parent,scroll);return;}
switch(parent.type){case'tab':if(typeof(window['nnTabs'])=="undefined"){self.stepThroughParents(parents,parent,scroll);break;}
parent.el.one('shown shown.bs.tab',function(){self.stepThroughParents(parents,parent,scroll);});nnTabs.show(parent.id);break;case'slider':if(typeof(window['nnSliders'])=="undefined"){self.stepThroughParents(parents,parent,scroll);break;}
parent.el.one('shown shown.bs.collapse',function(){self.stepThroughParents(parents,parent,scroll);});nnSliders.show(parent.id);break;}},getParent:function($el){if(!$el){return false;}
var $parent=$el.parent().closest('.nn_tabs-pane, .nn_sliders-body');if(!$parent.length){return false;}
return this.getElementArray($parent);},getElementArray:function($el){var id=$el.attr('data-toggle')?$el.attr('data-id'):$el.attr('id');var type=($el.hasClass('nn_tabs-pane')||$el.hasClass('nn_tabs-toggle'))?'tab':'slider';return{'type':type,'id':id,'el':type=='tab'?this.getTabElement(id):this.getSliderElement(id)};},fixEqualHeights:function(parent){var self=this;setTimeout((function(){self.fixEqualTabHeights(parent);}),250);},fixEqualTabHeights:function(parent){parent=parent?'div.nn_tabs-pane#'+parent.attr('data-id'):'div.nn_tabs';$(parent+' ul.nav-tabs').each(function(){var $lis=$(this).children();var height=0;$lis.each(function(){$(this).find('a').first().height('auto');});setTimeout((function(){$lis.each(function(){height=Math.max(height,$(this).find('a').first().height());});$lis.each(function(){$(this).find('a').first().height(height);});}),10);});},initActiveClasses:function(){$('li.nn_tabs-tab-sm').removeClass('active');},updateActiveClassesOnTabLinks:function(active_el){active_el.parent().parent().find('.nn_tabs-toggle').each(function($i,el){$('a.nn_tabs-link[data-id="'+$(el).attr('data-id')+'"]').each(function($i,el){var $link=$(el);if($link.attr('data-toggle')||$link.hasClass('nn_tabs-toggle-sm')||$link.hasClass('nn_sliders-toggle-sm')){return;}
if($link.attr('data-id')!==active_el.attr('data-id')){$link.removeClass('active');return;}
$link.addClass('active');});});},initEqualHeights:function(){var self=this;self.fixEqualHeights();$('a.nn_tabs-toggle').on('shown shown.bs.tab',function(){self.fixEqualHeights($(this));});$(window).resize(function(){self.fixEqualHeights();});},initHashLinkList:function(){var self=this;$('a[href^="#"],a[href^="'+this.current_url+'#"]').each(function($i,el){self.initHashLink(el);});},initHashLink:function(el){var self=this;var $link=$(el);if($link.attr('data-toggle')||$link.hasClass('nn_aliders-link')||$link.hasClass('nn_tabs-toggle-sm')||$link.hasClass('nn_sliders-toggle-sm')){return;}
var id=$link.attr('href').substr($link.attr('href').indexOf('#')+1);if(id==''){return;}
var scroll=false;var $anchor=$('a#anchor-'+id);if($anchor.length==0){$anchor=$('a[name="'+id+'"]');scroll=$anchor;}
if($anchor.length==0){return;}
if($anchor.closest('.nn_tabs').length==0){return;}
var $tab=$anchor.closest('.tab-pane').first();var tab_id=$tab.attr('id');if($link.closest('.nn_tabs').length>0){if($link.closest('.tab-pane').first().attr('id')==tab_id){return;}}
$link.click(function(e){e.preventDefault();self.openParents(tab_id);e.stopPropagation();});},initHashHandling:function(){if(window.history.replaceState){$('a.nn_tabs-toggle').on('shown shown.bs.tab',function(e){if($(this).closest('div.nn_tabs').hasClass('slideshow')){return;}
var id=$(this).attr('data-id');history.replaceState({},'','#'+id);e.stopPropagation();});}},initClickMode:function(){var self=this;$('body').on('click.tab.data-api','a.nn_tabs-toggle',function(e){var $el=$(this);e.preventDefault();nnTabs.show($el.attr('data-id'),$el.hasClass('nn_tabs-doscroll'));e.stopPropagation();});},initIframeReloading:function(){$('.tab-pane.active iframe').each(function(){$(this).attr('reloaded',true);});$('a.nn_tabs-toggle').on('show show.bs.tab',function(){if(typeof initialize=='function'){initialize();}
var $el=$('#'+$(this).attr('data-id'));$el.find('iframe').each(function(){if(this.src&&!$(this).attr('reloaded')){this.src+='';$(this).attr('reloaded',true);}});});$(window).resize(function(){if(typeof initialize=='function'){initialize();}
$('.tab-pane iframe').each(function(){$(this).attr('reloaded',false);});$('.tab-pane.active iframe').each(function(){if(this.src){this.src+='';$(this).attr('reloaded',true);}});});},getUrlVar:function(){var search='tab';var query=window.location.search.substring(1);if(query.indexOf(search+'=')==-1){return'';}
var vars=query.split('&');for(var i=0;i<vars.length;i++){var keyval=vars[i].split('=');if(keyval[0]!=search){continue;}
return keyval[1];}
return'';}};})
(jQuery);