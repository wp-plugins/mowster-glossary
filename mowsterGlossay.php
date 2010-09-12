<?php

    /*
        Plugin Name: mowsterGlossary
        Plugin URI: http://development.mowster.net
        Description: mowsterGlossary plugin is designed to give WordPress users an easy way to create and manage an online glossary of terms.
        Version:1.0.12
        Author: PedroDM
        Author URI: http://jobs.mowster.net
    */

    /*
        Initialize
    */
		if (!defined('DIRECTORY_SEPARATOR'))
		{
			if (strpos(php_uname('s'), 'Win') !== false )
			define('DIRECTORY_SEPARATOR', '\\');
			else
			define('DIRECTORY_SEPARATOR', '/');
		}
    
    $locale = get_locale();
		if ( !empty($locale) )
			load_textdomain('mowsterGL', dirname(__FILE__). DIRECTORY_SEPARATOR .'lang/mowster-glossary-'.$locale.'.mo');

    
    function Initialize ( )
    {

        $GLOBALS['mowsterGlossary']['Settings'] = _Get_Settings ( ) ;
        $Temporary = update_option ( 'rewrite_rules' , '' ) ;
        $url_check = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        
				$check = strtolower ( __ ( 'glossary', 'mowsterGL' ));

				if (strpos($url_check, $check) || strpos($url_check, 'mowsterGlossary.php') || strpos($url_check, 'page_id=') || strpos($url_check, 'edit.php?post_type=page')) { 		        

        
        $Temporary = require_once ABSPATH . 'wp-admin/upgrade-functions.php' ;

        $GLOBALS['mowsterGlossary']['Variables']['Table'] = $GLOBALS['table_prefix'] . 'mowster-glossary' ;
 
        $Temporary = $GLOBALS['wpdb']->get_var ( 'SHOW TABLES LIKE \'' . $GLOBALS['mowsterGlossary']['Variables']['Table'] . '\'' )  ;
    	  if ( $Temporary != $GLOBALS['mowsterGlossary']['Variables']['Table'] )
        {
          $Table = 'CREATE TABLE `' . $GLOBALS['mowsterGlossary']['Variables']['Table'] . '` ( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT , `Title` VARCHAR(255) NOT NULL , `Definition` TEXT NOT NULL , PRIMARY KEY ( `ID` ) ) ;' ;
    	    $Temporary = dbDelta ( $Table ) ;
        }
        

        $Author = Get_Page_Author ( ) ;

        $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] = Get_Page_ID ( ) ;
        while ( 0 == $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] )
        {
            $Array = Array
            (
                'post_author'           => $Author                  ,
                'post_date'             => date   ( 'Y-m-d H:i:s' ) ,
                'post_date_gmt'         => gmdate ( 'Y-m-d H:i:s' ) ,
                'post_content'          => '[[[mowsterGlossary]]]'  ,
                'post_title'            => __ ( 'Glossary', 'mowsterGL' ),
                'post_excerpt'          => ''                       ,
                'post_status'           => 'publish'                ,
                'comment_status'        => 'closed'                 ,
                'ping_status'           => 'closed'                 ,
                'post_password'         => ''                       ,
                'post_name'             => __ ( 'glossary', 'mowsterGL' ),
                'to_ping'               => ''                       ,
                'pinged'                => ''                       ,
                'post_modified'         => date   ( 'Y-m-d H:i:s' ) ,
                'post_modified_gmt'     => gmdate ( 'Y-m-d H:i:s' ) ,
                'post_content_filtered' => ''                       ,
                'post_parent'           => 0                        ,
                'guid'                  => ''                       ,
                'menu_order'            => 0                        ,
                'post_type'             => 'page'                   ,
                'post_mime_type'        => ''                       ,
                'comment_count'         => 0                        ,
            ) ;
            $Temporary = MySQL_Save ( $GLOBALS['table_prefix'] . 'posts' , $Array ) ;
            $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] = Get_Page_ID ( ) ;
        }

        

        $Temporary = Get_MySQL_Field_Get ( 'post_type' , $GLOBALS['table_prefix'] . 'posts' , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' , '' , '' ) ;
        if ( 'page' != $Temporary )
        {
            $Array = Array
            (
                'post_type' => 'page' ,
            ) ;
            $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
        }
        

        $Temporary = Get_MySQL_Field_Get ( 'post_author' , $GLOBALS['table_prefix'] . 'posts' , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' , '' , '' ) ;
        if ( $Author != $Temporary )
        {
            $Array = Array
            (
                'post_author' => $Author ,
            ) ;
            $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
        }
        

        $Temporary = Get_MySQL_Field_Get ( 'post_status' , $GLOBALS['table_prefix'] . 'posts' , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' , '' , '' ) ;
        if ( 'publish' != $Temporary )
        {
            $Array = Array
            (
                'post_status' => 'publish' ,
            ) ;
            $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
        }
        

        $Temporary = Get_MySQL_Field_Get ( 'comment_status' , $GLOBALS['table_prefix'] . 'posts' , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' , '' , '' ) ;
        if ( 'closed' != $Temporary )
        {
            $Array = Array
            (
                'comment_status' => 'open' ,
            ) ;
            $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
        }
        

        $Temporary = Get_MySQL_Field_Get ( 'ping_status' , $GLOBALS['table_prefix'] . 'posts' , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' , '' , '' ) ;
        if ( 'closed' != $Temporary )
        {
            $Array = Array
            (
                'ping_status' => 'closed' ,
            ) ;
            $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
        }
        
				}


        return TRUE ;
				
    }

    /*

        Options

    */

    function admin_menu ( )
    {

        $Temporary = add_options_page ( 'mowsterGlossary' , 'mowsterGlossary' , 10 , 'mowster-glossary/mowsterGlossary.php' , 'Options_Edit' ) ;
        $Temporary = add_management_page ( __ ( 'mowsterGlossary', 'mowsterGL' ) , __ ( 'mowsterGlossary', 'mowsterGL' ) , 5 , 'mowster-glossary/mowsterGlossary.php' , 'Actions' ) ;

        return TRUE ;

    }


    /*

        Settings

    */

    function _Get_Settings ( )
    {

        $Settings = Array ( ) ;

        $Settings['Number_Of_Terms_Per_Page'] = intval ( get_option ( 'mowster_Glossary_Terms_Per_Page' ) ) ;
        if ( empty ( $Settings['Number_Of_Terms_Per_Page'] ) OR 0 > $Settings['Number_Of_Terms_Per_Page'] )
        {
            $Settings['Number_Of_Terms_Per_Page'] = 5 ;
            if ( empty ( $Settings['Number_Of_Terms_Per_Page'] ) ) $Temporary = add_option ( 'mowster_Glossary_Terms_Per_Page' , $Settings['Number_Of_Terms_Per_Page']  , '' , 'no' ) ;
            else $Temporary = update_option ( 'mowster_Glossary_Terms_Per_Page' , $Settings['Number_Of_Terms_Per_Page'] ) ;            
        }
        
        
        return $Settings ;

    }

    function Options_Edit ( )
    {

        if ( !empty ( $_POST['action'] ) AND 'update' == $_POST['action'] ) $Temporary = Options_Save ( ) ;
        
				?>
				    <div class="wrap">
				        <h2><?php echo __( 'mowsterGlossary', 'mowsterGL' ) ; ?></h2>
				        <form action="options-general.php?page=<?php echo $_GET['page'] ; ?>" method="post">
				            <fieldset class="options">
				                <br><legend><?php echo __( 'Settings', 'mowsterGL' ) ; ?></legend>
				                <table class="editform optiontable">
				                   <tr>
				                        <th scope="row"><label for="mowster_Glossary_Terms_Per_Page"><?php echo __ ( 'Number of Terms per Page:', 'mowsterGL' ) ; ?></label></th>
				                        <td>
				                            <select name="mowster_Glossary_Terms_Per_Page" id="mowster_Glossary_Terms_Per_Page">
				
				<?php

        $Array = Array
        (
            5 ,
            10 ,
            15 ,
            20 ,
            25 ,
            50 ,
            75 ,
            100 ,
        ) ;

				?>
				
				                                <?php echo SELECT_Options ( Array ( 'All' => $Array , 'Default' => $GLOBALS['mowsterGlossary']['Settings']['Number_Of_Terms_Per_Page'] ) , 'No' ) ; ?>
				                            </select>
				                        </td>
				                    </tr>
				                </table>
				            </fieldset>
				            <p class="submit">
				                <input type="hidden" name="action" value="update"                                 />
				                <input type="submit" name="submit" value="<?php echo __( 'Update &raquo;', 'mowsterGL' ) ; ?>"/>
				            </p>
				        </form>
				    </div>
				
				<?php

        return TRUE ;

    }

    function Options_Save ( )
    {

        $Temporary = update_option ( 'mowster_Glossary_Terms_Per_Page' , $_POST['mowster_Glossary_Terms_Per_Page'] ) ;
        $GLOBALS['mowsterGlossary']['Settings'] = _Get_Settings ( ) ;

		?>
		
		    <div id="message" class="updated fade">
		        <p><strong><?php echo __ ( 'The Options were saved successfully!', 'mowsterGL' ) ; ?></strong></p>
		    </div>
		
		<?php

        return TRUE ;

    }

    /*

        Actions

    */

    function Actions ( )
    {

        if ( empty ( $_REQUEST['action'] ) ) $_REQUEST['action'] = NULL ;
        

        switch ( $_REQUEST['action'] )
        {
            case 'save'   :
                $Temporary = Term_Save              ( ) ;
                $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
                $Temporary = Page_Update            ( ) ;
                break ;
            case 'edit'   :
                $Temporary = Term_Edit ( '' ) ;
                break ;
            case 'update' :
                $Temporary = Term_Update            ( ) ;
                if ($Temporary == false) break;
                $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
                $Temporary = Page_Update            ( ) ;
                break ;
            case 'delete' :
                $Temporary = Term_Delete            ( ) ;
                if ($Temporary == false) break;
                $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
                $Temporary = Page_Update            ( ) ;
                break ;
            default :
                $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
        }

        return TRUE ;

    }

    /*

        Terms

    */

    function Terms_Overview_Private ( )
    {

        $Total_Number_Of_Terms = Get_MySQL_Field_Get ( 'COUNT(*)' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '' , '' , '' ) ;

        $Number_Of_Terms_Per_Page = $GLOBALS['mowsterGlossary']['Settings']['Number_Of_Terms_Per_Page'] ;

        $Total_Number_Of_Pages = ceil ( $Total_Number_Of_Terms / $Number_Of_Terms_Per_Page ) ;

        $Key = !empty ( $_REQUEST['Key'] ) ? $_REQUEST['Key'] : '1' ;

        if ( $Total_Number_Of_Pages > 0 )
        {
            if ( $Key > $Total_Number_Of_Pages ) $Key = $Total_Number_Of_Pages ;
            else if ( $Key < 1 ) $Key = 1 ;   
        }
        else $Key = 1 ;
        
        $Previous = $Key - 1 ;
        if ( $Previous < 1 ) $Previous = __ ( 'Previous', 'mowsterGL') ; 
        else $Previous = '<a href="edit.php?Key=' . $Previous . '&amp;page=' . $_REQUEST['page'] . '" title="'. __ ( 'Previous', 'mowsterGL') .'">'. __ ( 'Previous', 'mowsterGL') .'</a>' ;
        
        $Next = $Key + 1 ;
        if ( $Next > $Total_Number_Of_Pages ) $Next = __ ( 'Next', 'mowsterGL') ;
        else $Next = '<a href="edit.php?Key=' . $Next . '&amp;page=' . $_REQUEST['page'] . '" title="'. __ ( 'Next', 'mowsterGL') .'">'. __ ( 'Next', 'mowsterGL') .'</a>' ;
        

        $Limit = ( ( $Key - 1 ) * $Number_Of_Terms_Per_Page ) . ' , ' . $Number_Of_Terms_Per_Page ;

        $Terms = MySQL_Records_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '' , '`Title` ASC' , $Limit ) ;

				$logo_path = get_option('siteurl') . '/wp-content/plugins/mowster-glossary/images/mowsterGlossary_logo.gif';

				?>
				
				    <div class="wrap">
				
				        <h2><?php echo __ ( 'mowsterGlossary', 'mowsterGL' ) .' ( '.$Total_Number_Of_Terms.' '.__ ( 'terms', 'mowsterGL' ).' )'; ?>
				        <a id="jobs" href="http://jobs.mowster.net" target="_blank"><img src="<?php echo $logo_path; ?>" alt="jobs.mowster.net" title="jobs.mowster.net" style="vertical-align: middle; margin-left: 3px;"></a>
				        </h2>      
								
				<?php

        if ( !empty ( $Terms ) )
        {

            if ( $Total_Number_Of_Pages > 1 )
            {
                echo '<br>&nbsp;' ;
                $Temporary = Array ( ) ;
                for ( $Index = 1 ; $Index <= $Total_Number_Of_Pages ; $Index = $Index + 1 )
                {
                    if ( $Key == $Index )
                    
                        $Temporary[] = $Index ;
                    
                    else
                    
                        $Temporary[] = '<a href="edit.php?Key=' . $Index . '&amp;page=' . $_REQUEST['page'] . '" title="' . $Index . '">' . $Index . '</a>' ;
                    
                }
                $Temporary = implode ( ' - ' , $Temporary ) ;
                echo $Previous . ' - ' . $Temporary . ' - ' . $Next ;                
            }
            
            

						?>
									<br>
						      <table id="the-list-x" width="100%" style="background-color:#FFFFFF;">
							    <tr>
						            <th align="left" style="font-weight:bold; padding:3px;"></th>
						            <th valign="top" style="font-weight:bold; text-align: right; padding:3px;"><p><?php echo __ ( 'Term', 'mowsterGL'       ) ; ?></th>
						            <th align="left" style="font-weight:bold; padding:3px;"><p><?php echo __ ( 'Definition', 'mowsterGL' ) ; ?></th>
						            <th align="left" style="font-weight:bold; padding:3px;"></th>
						    	</tr>
						
										<script LANGUAGE="JavaScript">
												<!--
												function confirmPost(txt)
												{
												message = '<?php echo __ ( 'Erease the term', 'mowsterGL' ) ; ?> ' + txt + ' ?';
												var agree=confirm(message);
												if (agree)
												return true ;
												else
												return false ;
												}
												// -->
										</script>
						<?php

            $Class = '' ;

            foreach ( $Terms As $Term )
            {

                $Class = ( 'alternate' == $Class ) ? '' : 'alternate' ;
                $Edit = '<a href="edit.php?Terms_-_ID=' . $Term['ID'] . '&amp;Key=' . $Key . '&amp;action=edit&amp;page=' . $_REQUEST['page'] . '" class="edit">' . __ ( 'Edit', 'mowsterGL' ) . '</a>' ;
                $Delete = '<a href="edit.php?Terms_-_ID=' . $Term['ID'] . '&amp;Key=' . $Key . '&amp;action=delete&amp;page=' . $_REQUEST['page'] . '" class="delete" onClick="return confirmPost(\''.strtoupper($Term["Title"]).'\')"><img src="'.get_option ( 'siteurl' ).'/wp-content/plugins/mowster-glossary/images/delete.gif" title="' . __ ( 'Delete', 'mowsterGL' ) . '"></a>' ;

						?>
						
						        <tr id="cat-<?php echo $Term['ID'] ; ?>" class="<?php echo $Class?>">
						            <td valign="top" style="font-weight:bold; float: middle; padding:10px;"><p><?php echo $Delete ; ?></td>
						            <td valign="top" style="font-weight:bold; text-align: right; padding:10px; text-transform : uppercase;"><p><?php echo $Term['Title'] ; ?></td>            
						            <td valign="top" style="float: middle; padding:10px;"><p><?php echo $Term['Definition'] ; ?></td>            
						            <td valign="top" style="font-weight:bold; float: middle; padding:10px;"><p><?php echo $Edit ; ?></td>
						    	</tr>
						
						<?php

            }

						?>
            
						</table><br>&nbsp;
						
						<?php
					
            if ( $Total_Number_Of_Pages > 1 )
            {
                echo '<br>&nbsp;' ;
                $Temporary = Array ( ) ;
                for ( $Index = 1 ; $Index <= $Total_Number_Of_Pages ; $Index = $Index + 1 )
                {
                    if ( $Key == $Index ) $Temporary[] = $Index ;
                    else $Temporary[] = '<a href="edit.php?Key=' . $Index . '&amp;page=' . $_REQUEST['page'] . '" title="' . $Index . '">' . $Index . '</a>' ;
                    
                }
                $Temporary = implode ( ' - ' , $Temporary ) ;
                echo $Previous . ' - ' . $Temporary . ' - ' . $Next ;
                echo "<br>&nbsp;";
            }

        }
        else echo '<p>' . __ ( 'There are no Terms in the database.', 'mowsterGL' ) . '</p>' ;

        return TRUE ;

    }

    function Terms_Overview_Public ( $Content )
    {

        if ( $GLOBALS['post']->ID == $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] )
        {

            $Array = Array ( ) ;

            $permalink_structure = get_option ( 'permalink_structure' ) ;

            $Temporary = get_query_var ( 'Title' ) ;
						
            if ( empty ( $Temporary ) )
            {

                $Total_Number_Of_Terms = Get_MySQL_Field_Get ( 'COUNT(*)' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '' , '' , '' ) ;

                $Number_Of_Terms_Per_Page = $GLOBALS['mowsterGlossary']['Settings']['Number_Of_Terms_Per_Page'] ;

                $Total_Number_Of_Pages = ceil ( $Total_Number_Of_Terms / $Number_Of_Terms_Per_Page ) ;

                $Key = get_query_var ( 'Key' ) ;
                $Key = !empty ( $Key ) ? $Key : '1' ;
                if ( $Key > $Total_Number_Of_Pages ) $Key = $Total_Number_Of_Pages ;
                else if ( $Key < 1 ) $Key = 1 ;
                
                
                $Previous = $Key - 1 ;
                if ( $Previous < 1 ) $Previous = ' ' ;
                
                else
                {
                    if ( empty ( $permalink_structure ) ) $Previous = '<a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) . '&Key=' . $Previous . '" title="'.__ ( 'Previous', 'mowsterGL' ).'">&laquo;</a> ' ;
                    else $Previous = '<a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) .  __ ( 'page', 'mowsterGL' ).'/' . $Previous . '/' . '" title="'.__ ( 'Previous', 'mowsterGL' ).'">&laquo;</a> ' ; 
                }

                $Next = $Key + 1 ;
                if ( $Next > $Total_Number_Of_Pages ) $Next = ' ' ;
                
                else
                {
                    if ( empty ( $permalink_structure ) ) $Next = ' <a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) . '&Key=' . $Next . '" title="'.__ ( 'Next', 'mowsterGL' ).'">&raquo;</a>' ;
                    else $Next = ' <a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) . __ ( 'page', 'mowsterGL' ). '/' . $Next . '/' . '" title="'.__ ( 'Next', 'mowsterGL' ).'">&raquo;</a>' ;
                }

                $Limit = ( ( $Key - 1 ) * $Number_Of_Terms_Per_Page ) . ' , ' . $Number_Of_Terms_Per_Page ;

                $Terms = MySQL_Records_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '' , '`Title` ASC' , $Limit ) ;

                if ( !empty ( $Terms ) )
                {

                    foreach ( $Terms As $Term )
                    {
												if ($count == 4 && is_search()) {$Array[] = '<a class=\'more-link\' href='. get_permalink($page->ID).'>' . __ ( 'Read more...', 'mowsterGL' ) . '</a>'; break;}
                        $Array[] = '<span class="gloss"><h3><strong>' . $Term['Title'] . '</strong></h3></span>' ;
                        $Array[] = '<p>' . $Term['Definition'] . '</p>' ;
												$count++;
                    }
                    
                    $Serial_Number = ( $Key - 1 ) * $Number_Of_Terms_Per_Page ;

                    if ( $Total_Number_Of_Pages > 1 )
                    {
                        $Array[] = '<br><p>' ;
                        $Temporary = Array ( ) ;
                        for ( $Index = 1 ; $Index <= $Total_Number_Of_Pages ; $Index = $Index + 1 )
                        {
                            if ( $Key == $Index ) $Temporary[] = $Index ;
                            
                            else
                            {
                                if ( empty ( $permalink_structure ) ) $Temporary[] = '<a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) . '&Key=' . $Index . '" title="' . $Index . '">' . $Index . '</a>' ;
                                else $Temporary[] = '<a href="' . get_permalink ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) . '/'. __ ( 'page', 'mowsterGL' ). '/' . $Index . '/' . '" title="' . $Index . '">' . $Index . '</a>' ;
                            }
                        }
                        $Temporary = implode ( ' ' , $Temporary ) ;
                        if (!is_search())$Array[] = '<b>'. __ ( 'Part', 'mowsterGL' ).' </b>' . $Previous . $Temporary . $Next  ;
                        $Array[] = '</p>' ;
                    }

                }
                else $Array[] = '<p>' . __ ( 'There are no Terms in the database.', 'mowsterGL' ) . '</p>' ;

               

            }

            $Array = implode ( "\n" , $Array ) ;

            $Content = $Array ;

        }
        
        return $Content ;

    }

    function Term_Add ( )
    {

		?>

    <div class="wrap">
        <h2><?php echo __ ( 'mowsterGlossary : Add term', 'mowsterGL' ) ; ?></h2>
        <form action="tools.php?page=<?php echo $_GET['page'] ; ?>" method="post">
            <fieldset class="options">
                <table class="editform optiontable">
                    <tr>
                        <th scope="row" style="text-align: right;"><label for="Title"><?php echo __ ( 'Term:', 'mowsterGL' ) ; ?></label></th>
                        <td>
                            <input type="text" id="Title" name="Title" size="45" value="<?php echo $_POST['Title'];?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" scope="row"><label for="Definition"><?php echo __ ( 'Definition:', 'mowsterGL' ) ; ?></label></th>
                        <td>

												<script type="text/javascript" src="<?php echo get_option ( 'siteurl' );?>/wp-content/plugins/mowster-glossary/tiny_mce/tiny_mce.js"></script>
												<script type="text/javascript">
												tinyMCE.init({
												    mode : "textareas",
												    width:"600",
												    height:"300",
												    skin:"wp_theme",
												    theme : "advanced", 
														theme_advanced_buttons1: "bold,italic,underline,separator,bullist,numlist,separator,undo,redo,separator,copy,paste,separator,link,unlink,separator,pasteword,pastetext,separator,fullscreen,separator,code", 
														theme_advanced_buttons2: "",
														theme_advanced_buttons3: "",
														language:"pt",
														theme_advanced_toolbar_location : "top",
												    theme_advanced_toolbar_align : "left",
												    theme_advanced_statusbar_location : "bottom",
												    plugins : 'inlinepopups,safari,paste,fullscreen',
														object_resizing: "true",
												
												});
												</script>
												
												<textarea name="Definition"><?php echo $_POST['Definition'];?>
												</textarea>
                            
                        </td>
                    </tr>
                </table>
            </fieldset>
            <p class="submit">
                <input type="hidden" name="Key"     value="<?php echo $_REQUEST['Key'] ; ?>"     />
                <input type="hidden" name="action"  value="save"                                 />
                <input type="hidden" name="page"    value="<?php echo $_REQUEST['page'] ; ?>"    />
                <input type="submit" name="submit"  value="<?php echo __ ( 'Save &raquo;', 'mowsterGL' ) ; ?>"/>
            </p>
        </form>
    </div>

		<?php

        return TRUE ;

    }

    function Term_Save ( )
    {
    if (!$_POST['Title'] && !$_POST['Definition']) return FALSE;


    if (!$_POST['Title']) {
		?>
    <div id="message" class="updated fade">
        <p><strong><?php echo __ ( 'Error! Missing title.', 'mowsterGL' ) ; ?></strong></p>
    </div>
		<?php
		return FALSE;
    }
    
    if (!$_POST['Definition']) {
		?>
    <div id="message" class="updated fade">
        <p><strong><?php echo __ ( 'Error! Missing definition.', 'mowsterGL' ) ; ?></strong></p>
    </div>
		<?php
		return FALSE;
    }
    
    $Term = MySQL_Record_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '`Title` = \'' . $_POST['Title'] . '\'' , '' , '' ) ;
		if ($Term['Title']) {
		?>

    <div id="message" class="updated fade">
        <p><strong><?php echo __ ( 'Error! Existing Term.', 'mowsterGL' ) ; ?></strong></p>
    </div>

		<?php
    return TRUE;
    }
    
    $Array = Array
        (
            'Title'      => strtoupper(trim ( $_POST['Title'     ] )) ,
            'Definition' => trim ( $_POST['Definition'] ) ,
        ) ;

    $Temporary = MySQL_Save ( $GLOBALS['mowsterGlossary']['Variables']['Table'] , $Array ) ;
		
		?>

    <div id="message" class="updated fade">
        <p><strong><?php printf ( __ ( 'The Term %s was saved successfully.', 'mowsterGL' ), strtoupper($_POST['Title'])) ; ?></strong></p>
    </div>

		<?php

	  	unset ($_POST['Title'     ]);
			unset ($_POST['Definition'     ]);
    	return TRUE ;

    }

    function Term_Edit ( $error )
    {

        $Term = MySQL_Record_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '`ID` = \'' . $_REQUEST['Terms_-_ID'] . '\'' , '' , '' ) ;
        if (!$Term['Title']) {
        	      $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
                return;
        	}
		?>

    <div class="wrap">
        <h2><?php echo __ ( 'mowsterGlossary : Edit term', 'mowsterGL' ) ; ?></h2>
        <?php if ($error){ ?>
        <div id="message" class="updated fade">
		        <p><strong><?php echo $error; ?></strong></p>
		    </div>	
        <?php } ?>
        <form action="tools.php?page=<?php echo $_GET['page'] ; ?>" method="post">
            <fieldset class="options">
                <br>&nbsp;
                <table class="editform optiontable">
                    <tr>
                        <th scope="row"><label for="Title"><?php echo __ ( 'Term:', 'mowsterGL' ) ; ?></label></th>
                        <td>
                            <input type="text" id="Title" name="Title" style="text-transform: uppercase" value="<?php if (!$error) echo $Term['Title']; else echo $_POST['Title']; ?>" size="45"/>
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" scope="row"><label for="Definition"><?php echo __ ( 'Definition:', 'mowsterGL' ) ; ?></label></th>
                        <td>
                        <script type="text/javascript" src="<?php echo get_option ( 'siteurl' );?>/wp-content/plugins/mowster-glossary/tiny_mce/tiny_mce.js"></script>
												<script type="text/javascript">
												tinyMCE.init({
												    mode : "textareas",
												    width:"600",
												    height:"300",
												    skin:"wp_theme",
												    theme : "advanced", 
														theme_advanced_buttons1: "bold,italic,underline,separator,bullist,numlist,separator,undo,redo,separator,copy,paste,separator,link,unlink,separator,pasteword,pastetext,separator,fullscreen,separator,code", 
														theme_advanced_buttons2: "",
														theme_advanced_buttons3: "",
														language:"pt",
														theme_advanced_toolbar_location : "top",
												    theme_advanced_toolbar_align : "left",
												    theme_advanced_statusbar_location : "bottom",
												    plugins : 'inlinepopups,safari,paste,fullscreen',
														object_resizing: "true",
												
												});
												</script>
												
												<textarea id="Definition" name="Definition"><?php if (!$error) echo htmlentities ( $Term['Definition'] , ENT_QUOTES ); else echo htmlentities ( $_POST['Definition'] , ENT_QUOTES );  ?></textarea>
												</textarea>
                            
                        </td>
                    </tr>
                </table>
            </fieldset>
            <p class="submit">
                <input type="hidden" name="Terms_-_ID" value="<?php echo $Term['ID'] ; ?>"            />
                <input type="hidden" name="Key"        value="<?php echo $_REQUEST['Key'] ; ?>"       />
                <input type="hidden" name="action"     value="update"                                 />
                <input type="hidden" name="page"       value="<?php echo $_REQUEST['page'] ; ?>"      />
                <input type="submit" name="submit"     value="<?php echo __ ( 'Update &raquo;', 'mowsterGL' ) ; ?>"/>
            </p>
        </form>
    </div>

		<?php

        return TRUE ;

    }

    function Term_Update ( )
    {
		    if (!$_POST['Title'] && !$_POST['Definition']) return FALSE;
		    
		    if (!$_POST['Title']) {
        $error = __ ( 'Error! Missing title.', 'mowsterGL' ) ; 
				Term_Edit ( $error );
				return FALSE;
		    }
		    
		    if (!$_POST['Title']) {
        $error = __ ( 'Error! Missing definition.', 'mowsterGL' ) ; 
				Term_Edit ( $error );
				return FALSE;
		    }             
        
        $Term = MySQL_Record_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '`Title` = \'' . $_POST['Title'] . '\'' , '' , '' ) ;
		    
		    if ($Term['ID'] != $_POST['Terms_-_ID'] && $Term['ID'] != '') {
        $error = __ ( 'Error! Existing Term.', 'mowsterGL' ) ; 
				Term_Edit ( $error );
				return FALSE;
		    }         
      
        $_POST['Title'     ] = strtoupper($_POST['Title'     ]);
        $Array = Array
        (
            'Title'      => trim ( $_POST['Title'     ] ) ,
            'Definition' => trim ( $_POST['Definition'] ) ,
        ) ;

        $Temporary = MYSQL_Update ( $GLOBALS['mowsterGlossary']['Variables']['Table'] , $Array , '`ID` = \'' . $_POST['Terms_-_ID'] . '\'' ) ;

				?>
				
				    <div id="message" class="updated fade">
				        <p><strong><?php printf( __ ( 'The Term %s was updated successfully.', 'mowsterGL' ), strtoupper($_POST['Title']) ) ; ?></strong></p>
				    </div>
				
				<?php
	  		unset ($_POST['Title'     ]);
				unset ($_POST['Definition'     ]);  

        return TRUE ;

    }

    function Term_Delete ( )
    {
				$Term = MySQL_Record_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '`Id` = \'' . $_REQUEST['Terms_-_ID'] . '\'' , '' , '' ) ;
				if (!$Term['Title']){
				        $Temporary = Terms_Overview_Private ( ) ;
                $Temporary = Term_Add               ( ) ;
                return;
				}
				
				$Delete_Term = $Term['Title'];

        $Temporary = MYSQL_Delete ( $GLOBALS['mowsterGlossary']['Variables']['Table'] , '`ID` = \'' . $_REQUEST['Terms_-_ID'] . '\'' ) ;

		?>

    <div id="message" class="updated fade">
        <p><strong><?php printf ( __ ( 'The Term %s was deleted successfully.', 'mowsterGL' ), strtoupper($Delete_Term)); ?></strong></p>
    </div>

		<?php
        return TRUE ;

    }

    /*

        Pages

    */

    function Get_Page_ID ( )
    {
        $ID = Get_MySQL_Field_Get ( 'ID' , $GLOBALS['table_prefix'] . 'posts' , '`post_title` = \''. __ ( 'Glossary', 'mowsterGL' ).'\' AND `post_status` = \'publish\'' , '`ID` ASC' , '' ) ;        
        $ID = intval ( $ID ) ;
        return $ID ;
    }

    function Get_Page_Author ( )
    {
        $Author = Get_MySQL_Field_Get ( 'ID' ,  $GLOBALS['table_prefix'] . 'users' , '`user_login` = \'admin\'' , '' , '' ) ;
        if ( $Author > 0 )
        {
        }
        else
        {
            $Author = Get_MySQL_Field_Get ( 'MIN(`ID`)' , $GLOBALS['table_prefix'] . 'users' , '' , '' , '' ) ;
        }
        return $Author ;
    }


    function Permission_Get ( )
    {

	    $Value = get_post_meta ( $_GET['post'] , $GLOBALS['mowsterGlossary']['Variables']['Page']['Meta'] ) ;

        if ( empty ( $Value[0] ) ) $Value[0] = $GLOBALS['mowsterGlossary']['Settings']['Permission'] ;
        
        return TRUE ;

    }

    function Permission_Set ( $ID )
    {

        if ( empty ( $ID ) ) $ID = $_POST['post_ID'] ;

        $Key = $GLOBALS['mowsterGlossary']['Variables']['Page']['Meta'] ;

        $Value = $_POST[$GLOBALS['mowsterGlossary']['Variables']['Page']['Meta']] ;

        $Temporary = delete_post_meta ( $ID , $Key ) ;

        $Temporary = add_post_meta ( $ID , $Key , $Value ) ;

        return TRUE ;

    }

    function URLs_Variables ( $Variables )
    {

        $Temporary = array_push ( $Variables , 'Key' , 'Referer' , 'Title' ) ;

        return $Variables ;

    }

    function URLs_Rules ( $Rules )
    {

        if ( 0 != $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] )
        {

            $Temporary = get_page_uri ( $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] ) ;

            $Array = Array ( ) ;

            $Array['(' . $Temporary . ')/'. __ ( 'page', 'mowsterGL' ).'/([0-9]+)/?$'] = 'index.php?pagename=$matches[1]&Key=$matches[2]' ;

            $Rules = array_merge ( $Array , $Rules ) ;

        }

        return $Rules ;

    }

    /*
        MySQL
    */

    function MySQL_Result_Get ( $Query )
    {

        $GLOBALS['MySQL']['Queries'][] = $Query ;

        $Result = mysql_query ( $Query ) OR die ( 'Query: ' . $Query . '<br/>Error: ' . mysql_error ( ) ) ;

        return $Result ;

    }

    function MySQL_Records_Get ( $Columns , $Table , $Where , $Order_By , $Limit )
    {

        $Query = Array ( ) ;

        $Records = Array ( ) ;

        $Index = 0 ;

        $Query[] = 'SELECT ' . $Columns . ' FROM `' . $Table . '`' ;

        if ( !empty ( $Where ) ) $Query[] = 'WHERE ' . $Where ;
       
        if ( !empty ( $Order_By ) ) $Query[] = 'ORDER BY ' . $Order_By ;
        
        if ( !empty ( $Limit ) ) $Query[] = 'LIMIT ' . $Limit ;

        $Query = implode ( ' ' , $Query ) ;

        $Result = MySQL_Result_Get ( $Query ) ;

        if ( !empty ( $Result ) )
        {
            while ( $Row = mysql_fetch_assoc ( $Result ) )
            {
                $Index = $Index + 1 ;
                $Records[$Index] = $Row ;
            }
        }

        return $Records ;

    }

    function MySQL_Record_Get ( $Columns , $Table , $Where , $Order_By , $Limit )
    {

        $Query = Array ( ) ;

        $Record = Array ( ) ;

        $Query[] = 'SELECT ' . $Columns . ' FROM `' . $Table . '`' ;

        if ( !empty ( $Where ) ) $Query[] = 'WHERE ' . $Where ;
        
        if ( !empty ( $Order_By ) ) $Query[] = 'ORDER BY ' . $Order_By ;

        if ( !empty ( $Limit ) ) $Query[] = 'LIMIT ' . $Limit ;
        
        $Query = implode ( ' ' , $Query ) ;

        $Result = MySQL_Result_Get ( $Query ) ;

        if ( !empty ( $Result ) ) $Record = mysql_fetch_assoc ( $Result ) ;
        
        return $Record ;

    }

    function Get_MySQL_Field_Get ( $Column , $Table , $Where , $Order_By , $Limit )
    {

        $Query = Array ( ) ;

        $Field = NULL ;

        $Query[] = 'SELECT ' . $Column . ' FROM `' . $Table . '`' ;

        if ( !empty ( $Where ) ) $Query[] = 'WHERE ' . $Where ;
        
        if ( !empty ( $Order_By ) ) $Query[] = 'ORDER BY ' . $Order_By ;
        
        if ( !empty ( $Limit ) ) $Query[] = 'LIMIT ' . $Limit ;
        
        $Query = implode ( ' ' , $Query ) ;

        $Result = MySQL_Result_Get ( $Query ) ;

        if ( !empty ( $Result ) )
        {
            $Row = mysql_fetch_row ( $Result ) ;
            $Field = $Row[0] ;
        }

        return $Field ;

    }

    function MySQL_Save ( $Table , $Record )
    {

        $Query = Array ( ) ;

        $Keys = Array ( ) ;

        $Values = Array ( ) ;

        $Query[] = 'INSERT INTO `' . $Table . '`' ;

        $Query[] = '(' ;

        if ( !empty ( $Record ) )
        {
            foreach ( $Record As $Key => $Value )
            {
                $Keys[]   = '`'  . MySQL_Value_Get ( $Key   ) . '`'  ;
                $Values[] = '\'' . MySQL_Value_Get ( $Value ) . '\'' ;
            }
        }

        $Query[] = implode ( ' , ' , $Keys   ) ;
        $Query[] = ')' ;
        $Query[] = 'VALUES' ;
        $Query[] = '(' ;
        $Query[] = implode ( ' , ' , $Values ) ;
        $Query[] = ')' ;                         ;

        $Query = implode ( ' ' , $Query ) ;

        $Result = MySQL_Result_Get ( $Query ) ;

        return $Result ;

    }

    function MYSQL_Update ( $Table , $Record , $Where )
    {

        $Query = Array ( ) ;

        $Sets = Array ( ) ;

        $Query[] = 'UPDATE `' . $Table . '` SET' ;

        if ( !empty ( $Record ) )
        {
            foreach ( $Record As $Key => $Value )
            {
                $Key   = MySQL_Value_Get ( $Key   ) ;
                $Value = MySQL_Value_Get ( $Value ) ;
                $Sets[] = '`' . $Key . '` = \'' . $Value . '\'' ;
            }
        }

        $Query[] = implode ( ' , ' , $Sets ) ;

        $Query[] = 'WHERE ' . $Where ;

        $Query = implode ( ' ' , $Query ) ;

        $Result = MySQL_Result_Get ( $Query ) ;

        return $Result ;

    }

    function MYSQL_Delete ( $Table , $Where )
    {

        $Query = 'DELETE FROM `' . $Table . '` WHERE ' . $Where ;

        $Result = MySQL_Result_Get ( $Query ) ;

        return $Result ;

    }

    function MySQL_Value_Get ( $Variable )
    {

        return $Variable ;

    }

    /*
        Misc. Functions
    */

    function SELECT_Options ( $Values , $Indexed = 'Yes' )
    {

        $Options = Array ( ) ;

        if ( empty ( $Values['Default'] ) ) $Values['Default'] = NULL ;
        
        

        foreach ( $Values['All'] As $Key => $Value )
        {

            switch ( $Indexed )
            {
                case 'Yes' :
                    break ;
                case 'No' :
                    $Key = $Value ;
                    break ;
                default :
            }

            if ( $Key == $Values['Default'] ) $Selected = ' selected="selected"' ;
            else $Selected = '' ;
            
            $Options[] = '<option value="' . $Key . '"' . $Selected . '>' . $Value . '</option>' ;

        }

        $Options = implode ( '' , $Options ) ;

        return $Options ;

    }
    
    function Page_Update() {
        
        $Terms = MySQL_Records_Get ( '*' , $GLOBALS['mowsterGlossary']['Variables']['Table'] , '' , '`Title` ASC', ''  ) ;
        
        $post_excerpt = '<font color="#ff0000"><h3>'.__ ( 'Edit this on Tools &raquo; Glossary.', 'mowsterGL' ).'</h3></font><br>&nbsp;<br>';
        
        foreach ($Terms as &$value){
        		$post_excerpt = $post_excerpt . '<font color="#ffffff"><br /><h3>' . $value['Title'] . '</h3>' ;
        		$post_excerpt = $post_excerpt . '<br />' . $value['Definition'] . '<br /></font>' ;
        }
        
        
        $Array = Array
        (
            'post_content'      => trim ( $post_excerpt ) ,
        );
    	  $Temporary = MYSQL_Update ( $GLOBALS['table_prefix'] . 'posts' , $Array , '`ID` = \'' . $GLOBALS['mowsterGlossary']['Variables']['Page']['ID'] . '\'' ) ;
    }

    /*
        Initialize
    */
				
    $GLOBALS['mowsterGlossary'] = Array ( ) ;
    	
    $url_check = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    
    $Temporary = add_action ( 'admin_menu' , 'admin_menu' ) ;
		$Temporary = add_action ( 'init' , 'Initialize' ) ; 
		
		$check = strtolower ( __ ( 'glossary', 'mowsterGL' ));

		if (strpos($url_check, $check) || strpos($url_check, 'mowsterGlossary.php') || strpos($url_check, 'page_id=') || strpos($url_check, 'edit.php?post_type=page')) { 		        
     
     
		    $Temporary = add_action ( 'simple_edit_form'   , 'Permission_Get' ) ;
		    $Temporary = add_action ( 'edit_form_advanced' , 'Permission_Get' ) ;
		    $Temporary = add_action ( 'edit_page_form'     , 'Permission_Get' ) ;
		    $Temporary = add_action ( 'edit_post'          , 'Permission_Set' ) ;
		    $Temporary = add_action ( 'save_post'          , 'Permission_Set' ) ;
		    $Temporary = add_action ( 'publish_post'       , 'Permission_Set' ) ;
		
				$Temporary = add_filter ( 'query_vars' , 'URLs_Variables' ) ;
				$Temporary = add_filter ( 'rewrite_rules_array' , 'URLs_Rules' ) ;
		    $Temporary = add_filter ( 'the_excerpt' , 'Terms_Overview_Public' , 10 ) ;
		    $Temporary = add_filter ( 'the_content' , 'Terms_Overview_Public' , 10 ) ;

		}
?>
