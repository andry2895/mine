!function(e){var t=PhpDebugBar.utils.makecsscls("phpdebugbar-widgets-");PhpDebugBar.Widgets.TemplatesWidget=PhpDebugBar.Widget.extend({className:t("templates"),render:function(){this.$status=e("<div />").addClass(t("status")).appendTo(this.$el),this.$list=new PhpDebugBar.Widgets.ListWidget({itemRenderer:function(a,s){if(e("<span />").addClass(t("name")).text(s.name).appendTo(a),void 0!==s.xdebug_link&&null!==s.xdebug_link&&(s.xdebug_link.ajax?e('<a title="'+s.xdebug_link.url+'"></a>').on("click",function(){e.ajax(s.xdebug_link.url)}).addClass(t("editor-link")).appendTo(a):e('<a href="'+s.xdebug_link.url+'"></a>').addClass(t("editor-link")).appendTo(a)),s.render_time_str&&e('<span title="Render time" />').addClass(t("render-time")).text(s.render_time_str).appendTo(a),s.memory_str&&e('<span title="Memory usage" />').addClass(t("memory")).text(s.memory_str).appendTo(a),void 0!==s.param_count&&e('<span title="Parameter count" />').addClass(t("param-count")).text(s.param_count).appendTo(a),void 0!==s.type&&s.type&&e('<span title="Type" />').addClass(t("type")).text(s.type).appendTo(a),s.params&&!e.isEmptyObject(s.params)){var d=e('<table><tr><th colspan="2">Params</th></tr></table>').addClass(t("params")).appendTo(a);for(var r in s.params)"function"!=typeof s.params[r]&&d.append('<tr><td class="'+t("name")+'">'+r+'</td><td class="'+t("value")+'"><pre><code>'+s.params[r]+"</code></pre></td></tr>");a.css("cursor","pointer").click(function(){d.is(":visible")?d.hide():d.show()})}}}),this.$list.$el.appendTo(this.$el),this.$callgraph=e("<div />").addClass(t("callgraph")).appendTo(this.$el),this.bindAttr("data",function(a){this.$list.set("data",a.templates),this.$status.empty(),this.$callgraph.empty();var s=a.sentence||"templates were rendered";e("<span />").text(a.nb_templates+" "+s).appendTo(this.$status),a.accumulated_render_time_str&&this.$status.append(e('<span title="Accumulated render time" />').addClass(t("render-time")).text(a.accumulated_render_time_str)),a.memory_usage_str&&this.$status.append(e('<span title="Memory usage" />').addClass(t("memory")).text(a.memory_usage_str)),a.nb_blocks>0&&e("<div />").text(a.nb_blocks+" blocks were rendered").appendTo(this.$status),a.nb_macros>0&&e("<div />").text(a.nb_macros+" macros were rendered").appendTo(this.$status),void 0!==a.callgraph&&this.$callgraph.html(a.callgraph)})}})}(PhpDebugBar.$);