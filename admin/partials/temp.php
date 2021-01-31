<div class="wrap settings-wrap" id="page-settings">
    <h2>Настройки</h2>
    <div id="option-tree-header-wrap">
        <ul id="option-tree-header">
            <li id=""><a href="" target="_blank"></a>
            </li>
            <li id="option-tree-version"><span><?php _e('SEO Images Optimized','wpb-sio') ?></span>
            </li>
        </ul>
    </div>
    <div id="option-tree-settings-api">
    <div id="option-tree-sub-header"></div>
        <div class = "ui-tabs ui-widget ui-widget-content ui-corner-all">
           
          <!-- Tabs Begin-->
            <ul >
                <li id="tab_create_setting"><a href="#section_general">Основные настройки</a>
                </li>
                <li id="tab_faq" ><a href="#section_faq">FAQ</a>
                </li>
                <li id="tab_support" ><a href="#section_support">Поддержка</a>
                </li>
            </ul>
            <!-- Tabs End-->
            
            
    <div id="poststuff" class="metabox-holder">
        <div id="post-body">
			<div id="post-body-content">
                <div id="section_general" class = "postbox">
                    <div class="inside">
                        <div id="setting_theme_options_ui_text" class="format-settings">
                            <div class="format-setting-wrap">
                    
    <div class = "format-setting type-textarea has-desc">
        <div class = "format-setting-inner">            
    <form method="post" action="#section_general">
	<div class="format-setting-label">
		<h3 class="label">Основные настройки</h3>
	</div>
					
    <table class="form-table table_custom">
        
     <p class="">    
             %name<br>
             %title<br>
             %category
     </p>
       
        
        <tr valign="top">
        <th scope="row">Значение атрибута Alt</th>
        <td><input type="text" name="alt_value" value="<?php echo esc_attr( $options['alt_value'] ); ?>" />
        <p class="">Значение атрибута Alt будет заменено этой строкой</p>
        </td>
        </tr>
        
         <tr valign="top">
        <th scope="row">Перезаписать атрибут Alt</th>
        <td><select id="override_alt_value" name="override_alt_value">
		<?php $override_setting = array(
		'1'=> __('YES','wpb-sio'),
		'0'=> __('OFF','wpb-sio'), 
		'2'=> __('NO','wpb-sio')); 
		?>
		<?php foreach($override_setting as $key => $value) { ?>
		<option value="<?php echo $key; ?>" <?php if ($options['override_alt_value']==$key) { echo 'selected="selected"'; } ?> >
		<?php _e($value,'wpb-sio') ?> </option>
		<?php } ?>
		</select>
		<p class=""></p>
        </td>
        </tr>
        
       
        
         <tr valign="top">
        <th scope="row">Значение атрибута Title</th>
        <td><input type="text" name="title_value" value="<?php echo esc_attr( $options['title_value'] ); ?>" />
        <p class="">Значение атрибута Title будет заменено этой строкой</p>
        </td>
        </tr> 
    
       <tr valign="top">
        <th scope="row">Перезаписать атрибут Title</th>
        <td><select id="override_title_value" name="override_title_value">
		<?php 
		$override_setting = array(
			'1'=> __('YES','wpb-sio'),
			'0'=> __('OFF','wpb-sio'), 
			'2'=> __('NO','wpb-sio')); 
		?>
		<?php foreach($override_setting as $key => $value) { ?>
		<option value="<?php echo $key; ?>" <?php if ($options['override_title_value']==$key) { echo 'selected="selected"'; } ?> >
		<?php _e($value,'wpb-sio') ?> </option>
		<?php } ?>
		</select>
		<p class=""></p>
        </td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Перезаписать атрибут Alt и Title для миниатюр?</th>
        <td><select id="override_thumbnail" name="override_thumbnail">
		<?php 
		$override_setting = array(
			'1'=> __('YES','wpb-sio'),
			'0'=> __('OFF','wpb-sio'), 
			'2'=> __('NO','wpb-sio')); 
		?>
		<?php foreach($override_setting as $key => $value) { ?>
		<option value="<?php echo $key; ?>" <?php if ($options_array['override_thumbnail']==$key) { echo 'selected="selected"'; } ?> >
		<?php _e($value,'wpb-sio') ?> </option>
		<?php } ?>
		</select>
		<p class=""></p>
        </td>
        </tr>
        

        <tr valign="top">
        <th scope="row">Значение атрибута Alt для миниатюр</th>
        <td><input type="text" name="thumbnail_alt_value" value="<?php echo esc_attr( $options['thumbnail_alt_value'] ); ?>" />
        <p class=""></p>
        </td>
        </tr>
         
         <tr valign="top">
        <th scope="row">Значение атрибута Title для миниатюр</th>
        <td><input type="text" name="thumbnail_title_value" value="<?php echo esc_attr( $options['thumbnail_title_value'] ); ?>" />
        <p class=""></p>
        
        </td>
        </tr> 

         <tr valign="top">
        <th scope="row">Значения для подстановки</th>
        <td><input type="text" name="replace_value" value="<?php echo esc_attr( $options['replace_value'] ); ?>" />
        <p class="">Значения для подстановки, через запятую без пробелов.</p>
        
        </td>
        </tr> 
        
		
		<tr valign="top">
			<th scope="row">Включить поддержку Yoast primary category?</th>
        <td><select id="override_yost_primary_cat" name="override_yost_primary_cat">
		<?php 
		$override_setting = array(
			'1'=> __('YES','wpb-sio'),
			'0'=> __('NO','wpb-sio')); 
		?>
		<?php foreach($override_setting as $key => $value) { ?>
		<option value="<?php echo $key; ?>" <?php if ($options_array['override_yost_primary_cat']==$key) { echo 'selected="selected"'; } ?> >
		<?php _e($value,'wpb-sio') ?> </option>
		<?php } ?>
		</select>
		<p class="">Показать только primary category Yoast SEO Plugin</p>
        </td>
		</tr>
		
		
		</table>
		
		<table class="form-table ">  
		<tr valign="top">
        <td><input type="submit" name="submit_general_settings_tab" value="Save Changes" class="button button-primary"></td>
        <?php submit_button(__('Save Settings', $this->plugin_name), 'primary','submit', TRUE); ?>
        </tr>
		</table>
		
			
			</form>
                                        
					</div>
				</div>
			</div>
         </div>
        </div>
    </div>
    
            
    <div id="section_faq" class = "postbox">
        <div class="inside">
            <div class="format-settings">
                <div class="format-setting-wrap">
                    <div class="format-setting-label">
                    <h3 class="label"><?php _e('How does it work?','wpb-sio') ?> </h3>
                    </div>
                </div>
            </div>
                                
        <p><span class="description"><?php _e('1. The plugin dynamically replaces the alt tags with the pattern specified by you. It makes no changes to the database.','wpb-sio') ?>   </span></p>
        <p><span class="description"><?php _e('2. Since there are no changes to the database, one can have different alt tags for same images on different pages/posts.','wpb-sio') ?></span></p>
        <p><span class="description">3. %name - <?php _e('Will insert image name.','wpb-sio') ?></span></p>
        <p><span class="description">4. %title- <?php _e('Will insert post title.','wpb-sio') ?></span></p>
        <p><span class="description">5. %category - <?php _e('Will insert post categories.','wpb-sio') ?>  </span></p>                
				</div>
				
				  <div class="inside">
            <div class="format-settings">
                <div class="format-setting-wrap">
                    <div class="format-setting-label">
                    <h3 class="label"> <?php _e('Why optimize alt tags?','wpb-sio') ?> </h3>
                    </div>
                </div>
            </div>
                                
        <p><span class="description"><?php echo sprintf(__('1. According to <a target = "_blank" href = "http://googlewebmastercentral.blogspot.in/2007/12/using-alt-attributes-smartly.html"> this post </a> on the Google Webmaster Blog, Google tends to focus on the information in the ALT text. Creating a optimized alt tags can bring more traffic from Search Engines','wpb-sio')); ?> </span></p>
        
        <p><span class="description"><?php _e('2. Take note that the plugin does not make changes to the database. It dynamically replaces the tags at the times of page load.','wpb-sio') ?></span></p>
                      
				</div>
				
				
	</div>
	
	
	<div id="section_support" class = "postbox">
        <div class="inside">
            <div class="format-settings">
                <div class="format-setting-wrap">
                    <div class="format-setting-label">
                    <h3 class="label"><?php _e('Support','wpb-sio') ?> </h3>
                    </div>
                </div>
            </div>
                                
			<p><span class="description"><?php echo sprintf(__("1. For any queries contact us via the e-mail dev@wpbuild.ru</a>","wpb-soi")); ?> </span></p>

		</div>
	</div>
	

	
        </div>
    </div>
    </div>
        <div class="clear"></div>
        </div>
    </div>
</div>
