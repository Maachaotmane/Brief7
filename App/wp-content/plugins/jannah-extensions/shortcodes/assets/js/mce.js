(function() {

	'use strict';


	tinymce.PluginManager.add( 'jannah_extensions_mce_button', function( editor, url ) {

		// General shortcodes work with any theme ----------
		var generalShortcodes = [

			// Need Help? ----------
			{
				text: jannah_extensions_lang.shortcode_help,
				onclick: function(){
					window.open( jannah_extensions_lang.shortcode_docs_url, '_blank' );
				}
			},

			// [box] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_box,
				onclick: function(){
					editor.windowManager.open({
						title: jannah_extensions_lang.shortcode_box,
						body: [
							{
								type: 'listbox',
								name: 'typeOftheBox',
								label: jannah_extensions_lang.shortcode_style,
								'values': [
									{text: jannah_extensions_lang.shortcode_shadow,   value: 'shadow'},
									{text: jannah_extensions_lang.shortcode_info,     value: 'info'},
									{text: jannah_extensions_lang.shortcode_success,  value: 'success'},
									{text: jannah_extensions_lang.shortcode_warning,  value: 'warning'},
									{text: jannah_extensions_lang.shortcode_error,    value: 'error'},
									{text: jannah_extensions_lang.shortcode_download, value: 'download'},
									{text: jannah_extensions_lang.shortcode_note,     value: 'note'}
								]
							},
							{
								type: 'listbox',
								name: 'boxAlignment',
								label: jannah_extensions_lang.shortcode_alignment,
								'values': [
									{text: '', value: ''},
									{text: jannah_extensions_lang.shortcode_right,  value: 'alignright'},
									{text: jannah_extensions_lang.shortcode_left,   value: 'alignleft'},
									{text: jannah_extensions_lang.shortcode_center, value: 'aligncenter'}
								]
							},
							{
								type: 'textbox',
								name: 'CustomClass',
								label: jannah_extensions_lang.shortcode_class,
								value: ''
							},
							{
								type: 'textbox',
								name: 'BoxWidth',
								label: jannah_extensions_lang.shortcode_width,
								value: ''
							},
							{
								type: 'textbox',
								name: 'BoxContent',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[box type="' + e.data.typeOftheBox + '" align="' + e.data.boxAlignment + '" class="' + e.data.CustomClass + '" width="' + e.data.BoxWidth + '"]'+ e.data.BoxContent +'[/box]');
						}
					});
				}
			},

			// [button] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_button,
				onclick: function() {
					editor.windowManager.open({
						title: jannah_extensions_lang.shortcode_button,
						body: [
							{
								type: 'listbox',
								name: 'ButtonColor',
								label: jannah_extensions_lang.shortcode_color,
								'values': [
									{text: jannah_extensions_lang.shortcode_primary_color, value: 'primary'},
									{text: jannah_extensions_lang.shortcode_red,           value: 'red'},
									{text: jannah_extensions_lang.shortcode_orange,        value: 'orange'},
									{text: jannah_extensions_lang.shortcode_blue,          value: 'blue'},
									{text: jannah_extensions_lang.shortcode_green,         value: 'green'},
									{text: jannah_extensions_lang.shortcode_black,         value: 'black'},
									{text: jannah_extensions_lang.shortcode_gray,          value: 'gray'},
									{text: jannah_extensions_lang.shortcode_white,         value: 'white'},
									{text: jannah_extensions_lang.shortcode_pink,          value: 'pink'},
									{text: jannah_extensions_lang.shortcode_purple,        value: 'purple '}
								]
							},
							{
								type: 'listbox',
								name: 'ButtonSize',
								label: jannah_extensions_lang.shortcode_size,
								'values': [
									{text: jannah_extensions_lang.shortcode_small,  value: 'small'},
									{text: jannah_extensions_lang.shortcode_medium, value: 'medium'},
									{text: jannah_extensions_lang.shortcode_big,    value: 'big'}
								]
							},
							{
								type: 'textbox',
								name: 'ButtonLink',
								label: jannah_extensions_lang.shortcode_link,
								minWidth: 300,
								value: 'http://'
							},
							{
								type: 'textbox',
								name: 'ButtonText',
								label: jannah_extensions_lang.shortcode_text,
								value: ''
							},
							{
								type: 'textbox',
								name: 'ButtonIcon',
								label: jannah_extensions_lang.shortcode_icon,
								value: ''
							},
							{
								type: 'checkbox',
								name: 'ButtonTarget',
								label: jannah_extensions_lang.shortcode_new_window,
								value: 'blank',
							},
							{
								type: 'checkbox',
								name: 'ButtonNofollow',
								label: jannah_extensions_lang.shortcode_nofollow,
								value: 'blank',
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[button color="' + e.data.ButtonColor + '" size="' + e.data.ButtonSize + '" link="' + e.data.ButtonLink + '" icon="' + e.data.ButtonIcon + '" target="' + e.data.ButtonTarget + '" nofollow="' + e.data.ButtonNofollow + '"]'+ e.data.ButtonText +'[/button]');
						}
					});
				}
			},

			// [tabs] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_tabs,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_tabs,
						width: 300,
						height: 60,
						body: [
							{
								type: 'listbox',
								name: 'TabType',
								label: jannah_extensions_lang.shortcode_style,
								'values': [
									{text: jannah_extensions_lang.shortcode_horizontal, value: 'horizontal'},
									{text: jannah_extensions_lang.shortcode_vertical, value: 'vertical'}
								]
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '\
								[tabs type="'+ e.data.TabType +'"]<br />\
									[tabs_head]<br />\
										[tab_title] '+ jannah_extensions_lang.shortcode_tab_title1 +' [/tab_title]<br />\
										[tab_title] '+ jannah_extensions_lang.shortcode_tab_title2 +' [/tab_title]<br />\
										[tab_title] '+ jannah_extensions_lang.shortcode_tab_title3 +' [/tab_title]<br />\
									[/tabs_head]<br />\
									[tab] '+ jannah_extensions_lang.shortcode_tab_content1 +' [/tab]<br />\
									[tab] '+ jannah_extensions_lang.shortcode_tab_content2 +' [/tab]<br />\
									[tab] '+ jannah_extensions_lang.shortcode_tab_content3 +' [/tab]<br />\
								[/tabs]\
							');
						}
					},
					{
						plugin_url : url
					});
				}
			},

			// [toggle] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_toggle,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_toggle,
						body: [
							{
								type: 'textbox',
								name: 'ToggleTitle',
								label: jannah_extensions_lang.shortcode_title,
								value: ''
							},
							{
								type: 'listbox',
								name: 'ToggleState',
								label: jannah_extensions_lang.shortcode_state,
								'values': [
									{text: jannah_extensions_lang.shortcode_opened, value: 'open'},
									{text: jannah_extensions_lang.shortcode_closed, value: 'close'},
								]
							},
							{
								type: 'textbox',
								name: 'ToggleContent',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[toggle title="' + e.data.ToggleTitle + '" state="' + e.data.ToggleState + '"]'+ e.data.ToggleContent +'[/toggle]');
						}
					});
				}
			},

			// [slideshow] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_slideshow,
				onclick: function() {
					editor.insertContent( '\
						[tie_slideshow]<br /><br />\
							[tie_slide] '+ jannah_extensions_lang.shortcode_slide1 +' [/tie_slide]<br /><br />\
							[tie_slide] '+ jannah_extensions_lang.shortcode_slide2 +' [/tie_slide]<br /><br />\
							[tie_slide] '+ jannah_extensions_lang.shortcode_slide3 +' [/tie_slide]<br /><br />\
						[/tie_slideshow]\
					');
				}
			},

			// [author] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_bio,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_bio,
						body: [
							{
								type: 'textbox',
								name: 'AuthorTitle',
								label: jannah_extensions_lang.shortcode_title,
								value: ''
							},
							{
								type: 'textbox',
								name: 'AuthorImageURL',
								label: jannah_extensions_lang.shortcode_avatar,
								value: 'http://'
							},
							{
								type: 'textbox',
								name: 'AuthorContent',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[author title="' + e.data.AuthorTitle + '" image="' + e.data.AuthorImageURL + '"]'+ e.data.AuthorContent +'[/author]');
						}
					});
				}
			},

			// [flickr] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_flickr,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_add_flickr,
						body: [
							{
								type: 'textbox',
								name: 'AccountID',
								label: jannah_extensions_lang.shortcode_flickr_id,
								value: ''
							},
							{
								type: 'textbox',
								name: 'NumberPhotos',
								label: jannah_extensions_lang.shortcode_flickr_num,
								value: '5'
							},
							{
								type: 'listbox',
								name: 'FlickrSorting',
								label: jannah_extensions_lang.shortcode_sorting,
								'values': [
									{text: jannah_extensions_lang.shortcode_recent, value: 'latest'},
									{text: jannah_extensions_lang.shortcode_random, value: 'random'},
								]
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[flickr id="' + e.data.AccountID + '" number="' + e.data.NumberPhotos + '" orderby="' + e.data.FlickrSorting + '"]');
						}
					});
				}
			},

			// [feed] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_feed,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_feed,
						body: [
							{
								type: 'textbox',
								name: 'RSSurl',
								label: jannah_extensions_lang.shortcode_feed_url,
								minWidth: 300,
								value: 'http://'
							},
							{
								type: 'textbox',
								name: 'NumberFeeds',
								label: jannah_extensions_lang.shortcode_feeds_num,
								value: '5'
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[feed url="' + e.data.RSSurl + '" number="' + e.data.NumberFeeds + '"]');
						}
					});
				}
			},

			// [tooltip] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_tooltip,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_tooltip,
						body: [
							{
								type: 'textbox',
								name: 'ToolTipContent',
								label: jannah_extensions_lang.shortcode_title,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 50
							},
							{
								type: 'textbox',
								name: 'ToolTipText',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							},
							{
								type: 'listbox',
								name: 'ToolTipGravities',
								label: jannah_extensions_lang.shortcode_direction,
								'values': [
									{value: 'n', text: jannah_extensions_lang.shortcode_top},
									{value: 'w', text: jannah_extensions_lang.shortcode_left},
									{value: 'e', text: jannah_extensions_lang.shortcode_right},
									{value: 's', text: jannah_extensions_lang.shortcode_bottom},
								]
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[tie_tooltip text="' + e.data.ToolTipText + '" gravity="' + e.data.ToolTipGravities + '"]'+ e.data.ToolTipContent +'[/tie_tooltip]');
						}
					});
				}
			},

			// [social] Shortcodes ----------
			{
				text:  jannah_extensions_lang.shortcode_share,
				onclick: function() {
					editor.windowManager.open( {
						title:  jannah_extensions_lang.shortcode_share,
						body: [
							{
								type: 'checkbox',
								name: 'Facebook',
								label: jannah_extensions_lang.shortcode_facebook,
							},
							{
								type: 'checkbox',
								name: 'Tweet',
								label: jannah_extensions_lang.shortcode_tweet,
							},
							{
								type: 'checkbox',
								name: 'Pinterest',
								label: jannah_extensions_lang.shortcode_pinterest,
							},
							{
								type: 'label',
								name: 'TwitterFollowButton',
								onPostRender : function() {
									this.getEl().innerHTML = "<br /><strong>"+jannah_extensions_lang.shortcode_follow+"</strong>"
								}
							},
							{
								type: 'checkbox',
								name: 'Twitter',
								label: jannah_extensions_lang.shortcode_follow,
							},
							{
								type: 'textbox',
								name: 'TwitterUsername',
								label: jannah_extensions_lang.shortcode_username,
								value: '',
								minWidth: 200,
							},
						],
						onsubmit: function( e ) {
							if( e.data.Facebook ) {
								editor.insertContent( '[facebook]');
							}
							if( e.data.Tweet ) {
								editor.insertContent( '[tweet]');
							}
							if( e.data.Pinterest ) {
								editor.insertContent( '[pinterest]');
							}
							if( e.data.Twitter ) {
								editor.insertContent( '[follow id="'+e.data.TwitterUsername+'" count="true" ]');
							}
						}
					});
				}
			},

			// [dropcap] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_dropcap,
				onclick: function() {
					editor.insertContent( '[dropcap]' + editor.selection.getContent() + '[/dropcap]' );
				}
			},

			// [tags] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_tags,
				onclick: function() {
					editor.insertContent( '[tie_tags]' );
				}
			},

			// [highlight] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_highlight,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_highlight,
						minWidth: 400,
						body: [
							{
								type: 'listbox',
								name: 'color',
								label: jannah_extensions_lang.shortcode_color,
								'values': [
									{text: jannah_extensions_lang.shortcode_yellow, value: 'yellow'},
									{text: jannah_extensions_lang.shortcode_red,    value: 'red'},
									{text: jannah_extensions_lang.shortcode_blue,   value: 'blue'},
									{text: jannah_extensions_lang.shortcode_orange, value: 'orange'},
									{text: jannah_extensions_lang.shortcode_green,  value: 'green'},
									{text: jannah_extensions_lang.shortcode_gray,   value: 'gray'},
									{text: jannah_extensions_lang.shortcode_black,  value: 'black'},
									{text: jannah_extensions_lang.shortcode_pink,   value: 'pink'},
								]
							},
							{
								type: 'textbox',
								name: 'highlightedText',
								label: jannah_extensions_lang.shortcode_text,
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[highlight color="'+ e.data.color+ '"]' + e.data.highlightedText + '[/highlight]' );
						}
					});
				}
			},

			// [padding] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_padding,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_padding,
						body: [
							{
								type: 'textbox',
								name: 'left',
								label: jannah_extensions_lang.shortcode_padding_left,
								value: '10%',
							},
							{
								type: 'textbox',
								name: 'right',
								label: jannah_extensions_lang.shortcode_padding_right,
								value: '10%',
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[padding right="'+ e.data.right+ '" left="'+ e.data.left+ '"]' + editor.selection.getContent() + '[/padding]' );
						}
					});
				}
			},

			// [divider] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_divider,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_divider,
						body: [
							{
								type: 'listbox',
								name: 'style',
								label: jannah_extensions_lang.shortcode_style,
								'values': [
									{text: jannah_extensions_lang.shortcode_solid,  value: 'solid'},
									{text: jannah_extensions_lang.shortcode_dashed, value: 'dashed'},
									{text: jannah_extensions_lang.shortcode_normal, value: 'normal'},
									{text: jannah_extensions_lang.shortcode_double, value: 'double'},
									{text: jannah_extensions_lang.shortcode_dotted, value: 'dotted'}
								]
							},
							{
								type: 'textbox',
								name: 'MarginTop',
								label: jannah_extensions_lang.shortcode_margin_top,
								value: '20',
							},
							{
								type: 'textbox',
								name: 'MarginBottom',
								label: jannah_extensions_lang.shortcode_margin_bottom,
								value: '20',
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[divider style="'+ e.data.style+ '" top="'+ e.data.MarginTop+ '" bottom="'+ e.data.MarginBottom+ '"]' );
						}
					});
				}
			},

			// [tie_list] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_lists,
				menu: [
					{
						text: jannah_extensions_lang.shortcode_star,
						onclick: function() {
							editor.insertContent( '[tie_list type="starlist"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_check,
						onclick: function() {
							editor.insertContent( '[tie_list type="checklist"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_thumb_up,
						onclick: function() {
							editor.insertContent( '[tie_list type="thumbup"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_thumb_down,
						onclick: function() {
							editor.insertContent( '[tie_list type="thumbdown"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_plus,
						onclick: function() {
							editor.insertContent( '[tie_list type="plus"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_minus,
						onclick: function() {
							editor.insertContent( '[tie_list type="minus"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_heart,
						onclick: function() {
							editor.insertContent( '[tie_list type="heart"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_light_bulb,
						onclick: function() {
							editor.insertContent( '[tie_list type="lightbulb"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_cons,
						onclick: function() {
							editor.insertContent( '[tie_list type="cons"]' + editor.selection.getContent() + '[/tie_list]' );
						}
					},
				]
			},

			// [is_logged_in] and [is_guest] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_Restrict,
				menu: [
					{
						text: jannah_extensions_lang.shortcode_registered,
						onclick: function() {
							editor.insertContent( '[is_logged_in]' + editor.selection.getContent() + '[/is_logged_in]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_guests,
						onclick: function() {
							editor.insertContent( '[is_guest]' + editor.selection.getContent() + '[/is_guest]' );
						}
					},
				]
			},

			// [Columns] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_columns,
				menu: [
					{
						text: '[1/1]',
						onclick: function() {
							editor.insertContent( '[one_half]'+jannah_extensions_lang.shortcode_add_content+'[/one_half][one_half_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_half_last]' );
						}
					},
					{
						text: '[1/1/1]',
						onclick: function() {
							editor.insertContent( '[one_third]'+jannah_extensions_lang.shortcode_add_content+'[/one_third][one_third]'+jannah_extensions_lang.shortcode_add_content+'[/one_third][one_third_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_third_last]' );
						}
					},
					{
						text: '[1/1/1/1]',
						onclick: function() {
							editor.insertContent( '[one_fourth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fourth][one_fourth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fourth][one_fourth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fourth][one_fourth_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_fourth_last]' );
						}
					},
					{
						text: '[1/1/1/1/1]',
						onclick: function() {
							editor.insertContent( '[one_fifth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fifth][one_fifth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fifth][one_fifth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fifth][one_fifth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fifth][one_fifth_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_fifth_last]' );
						}
					},
					{
						text: '[1/1/1/1/1/1]',
						onclick: function() {
							editor.insertContent( '[one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][one_sixth_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth_last]' );
						}
					},
					{
						text: '[1/3]',
						onclick: function() {
							editor.insertContent( '[one_fourth]'+jannah_extensions_lang.shortcode_add_content+'[/one_fourth][three_fourth_last]'+jannah_extensions_lang.shortcode_add_content+'[/three_fourth_last]' );
						}
					},
					{
						text: '[1/5]',
						onclick: function() {
							editor.insertContent( '[one_sixth]'+jannah_extensions_lang.shortcode_add_content+'[/one_sixth][five_sixth_last]'+jannah_extensions_lang.shortcode_add_content+'[/five_sixth_last]' );
						}
					},
					{
						text: '[2/1]',
						onclick: function() {
							editor.insertContent( '[two_third]'+jannah_extensions_lang.shortcode_add_content+'[/two_third][one_third_last]'+jannah_extensions_lang.shortcode_add_content+'[/one_third_last]' );
						}
					},
					{
						text: '[2/3]',
						onclick: function() {
							editor.insertContent( '[two_fifth]'+jannah_extensions_lang.shortcode_add_content+'[/two_fifth][three_fifth_last]'+jannah_extensions_lang.shortcode_add_content+'[/three_fifth_last]' );
						}
					},
				]
			}
		],


		// Theme shortcodes works with TieLabs theme only ----------
		themeShortcodes = [

			// [googlemap] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_map,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_map,
						body: [
							{
								type: 'textbox',
								name: 'MapURL',
								label: jannah_extensions_lang.shortcode_map_url,
								minWidth: 300,
								value: 'http://'
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[googlemap src="' + e.data.MapURL + '"]');
						}
					});
				}
			},

			// [embed] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_video,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_video,
						body: [
							{
								type: 'textbox',
								name: 'VideoURL',
								label: jannah_extensions_lang.shortcode_video_url,
								value: 'http://',
								minWidth: 300,
							},
							{
								type: 'textbox',
								name: 'VideoWidth',
								label: jannah_extensions_lang.shortcode_width,
								value: ''
							},
							{
								type: 'textbox',
								name: 'Videoheight',
								label: jannah_extensions_lang.shortcode_height,
								value: ''
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[embed width="' + e.data.VideoWidth + '" height="' + e.data.Videoheight + '"]' + e.data.VideoURL + '[/embed]');
						}
					});
				}
			},

			// [audio] Shortcode ----------
			{
				text: jannah_extensions_lang.shortcode_audio,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_audio,
						body: [
							{
								type: 'textbox',
								name: 'mp3URL',
								label: jannah_extensions_lang.shortcode_mp3,
								value: 'http://',
								minWidth: 300,
							},
							{
								type: 'textbox',
								name: 'm4aURL',
								label: jannah_extensions_lang.shortcode_m4a,
								value: 'http://',
								minWidth: 300,
							},
							{
								type: 'textbox',
								name: 'oggURL',
								label: jannah_extensions_lang.shortcode_ogg,
								value: 'http://',
								minWidth: 300,
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[audio mp3="' + e.data.mp3URL + '" m4a="' + e.data.m4aURL + '" ogg="' + e.data.oggURL + '"]');
						}
					});
				}
			},

			// [lightbox] Shortcode ----------
			{
				text:  jannah_extensions_lang.shortcode_lightbox,
				onclick: function() {
					editor.windowManager.open( {
						title:  jannah_extensions_lang.shortcode_lightbox,
						body: [
							{
								type: 'textbox',
								name: 'lightBoxURL',
								label: jannah_extensions_lang.shortcode_lightbox_url,
								value: 'http://',
								minWidth: 300,
							},
							{
								type: 'textbox',
								name: 'lightBoxTitle',
								label: jannah_extensions_lang.shortcode_title,
								value: '',
								minWidth: 300,
							},
							{
								type: 'textbox',
								name: 'lightBoxContent',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							}
						],
						onsubmit: function( e ) {
							editor.insertContent( '[lightbox full="' + e.data.lightBoxURL + '" title="' + e.data.lightBoxTitle + '"]'+ e.data.lightBoxContent +'[/lightbox]');
						}
					});
				}
			},

			// <blockquote> ----------
			{
				text: jannah_extensions_lang.shortcode_blockquote,
				onclick: function(){
					editor.windowManager.open({
						title: jannah_extensions_lang.shortcode_blockquote,
						body: [
							{
								type: 'textbox',
								name: 'blockquoteContent',
								label: jannah_extensions_lang.shortcode_content,
								value: '',
								multiline: true,
								minWidth: 300,
								minHeight: 100
							},
							{
								type: 'listbox',
								name: 'blockquoteAlignment',
								label: jannah_extensions_lang.shortcode_alignment,
								'values': [
									{text: '', value: ''},
									{text: jannah_extensions_lang.shortcode_right,  value: 'alignright'},
									{text: jannah_extensions_lang.shortcode_left,   value: 'alignleft'},
									{text: jannah_extensions_lang.shortcode_center, value: 'aligncenter'}
								]
							},
							{
								type: 'textbox',
								name: 'blockquoteAuthor',
								label: jannah_extensions_lang.shortcode_author,
								value: ''
							},
							{
								type: 'listbox',
								name: 'blockquoteStyle',
								label: jannah_extensions_lang.shortcode_style,
								'values': [
									{text: jannah_extensions_lang.shortcode_dark,   value: ''},
									{text: jannah_extensions_lang.shortcode_light,  value: 'quote-light'},
									{text: jannah_extensions_lang.shortcode_simple, value: 'quote-simple'}
								]
							},
						],
						onsubmit: function( e ) {

							var author = '';
							if( e.data.blockquoteAuthor ) {
								author = ' <cite>'+ e.data.blockquoteAuthor +'</cite>';
							}

							editor.insertContent( '<blockquote class="' + e.data.blockquoteAlignment + ' ' + e.data.blockquoteStyle + ' ">'+ e.data.blockquoteContent + author + '</blockquote><p>&nbsp;</p>');
						}
					});
				}
			},

			// [ads] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_ads,
				menu: [
					{
						text: jannah_extensions_lang.shortcode_ads1,
						onclick: function() {
							editor.insertContent( '[ads1]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_ads2,
						onclick: function() {
							editor.insertContent( '[ads2]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_ads3,
						onclick: function() {
							editor.insertContent( '[ads3]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_ads4,
						onclick: function() {
							editor.insertContent( '[ads4]' );
						}
					},
					{
						text: jannah_extensions_lang.shortcode_ads5,
						onclick: function() {
							editor.insertContent( '[ads5]' );
						}
					},
				]
			},

			// [tie_login] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_login,
				onclick: function() {
					editor.insertContent( '[tie_login]' );
				}
			},

			// [tie_full_img] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_full_img,
				onclick: function() {
					editor.insertContent( '[tie_full_img]' + editor.selection.getContent() + '[/tie_full_img]' );
				}
			},

			// [Content Index] Shortcodes ----------
			{
				text: jannah_extensions_lang.shortcode_index,
				onclick: function() {
					editor.windowManager.open( {
						title: jannah_extensions_lang.shortcode_index,
						minWidth: 400,
						body: [
							{
								type: 'textbox',
								name: 'indexText',
								label: jannah_extensions_lang.shortcode_text,
							},
						],
						onsubmit: function( e ) {
							editor.insertContent( '[tie_index]' + e.data.indexText + '[/tie_index]' );
						}
					});
				}
			},

		],

		allShortcodes = generalShortcodes;


		// if the theme is active add the custom theme shortcodes
		if( jannah_extensions_lang.jannah_theme_active ){
			allShortcodes = generalShortcodes.concat(themeShortcodes);
		}


		// Add the button ----------
		editor.addButton( 'jannah_extensions_mce_button', {
			icon    : ' tie-shortcodes-icon ',
			tooltip : jannah_extensions_lang.shortcode_tielabs,
			type    : 'menubutton',
			minWidth: 210,
			menu: allShortcodes
		});

	});

})();
