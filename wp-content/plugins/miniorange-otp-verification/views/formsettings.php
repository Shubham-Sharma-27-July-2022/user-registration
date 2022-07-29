<?php

echo '<div class="mo_registration_table_layout" id="selected_form_details">
					<table id="mo_forms" style="width: 100%;">
						<tr>
							<td>
								<h2>
									<i>'.mo_("FORM SETTINGS").'</i>
									<span style="float:right;margin-top:-10px;">
									    <a  class="show_configured_forms button button-primary button-large" 
                                            href="'.mo_esc_string($action,"url").'">
                                            '.mo_("Show All Enabled Forms").'
                                        </a>
									    <a class="show_form_list button button-primary button-large"
									        href="'.mo_esc_string($formsListPage,"url").'">
									        '.mo_("Show Forms List").'
                                        </a>
                                        <input  name="save" id="ov_settings_button" '.mo_esc_string($disabled,"attr").' 
                                                class="button button-primary button-large" 
                                                value="'.mo_("Save Settings").'" type="submit">
                                        <span   class="mo-dashicons dashicons dashicons-arrow-up toggle-div" 
                                                data-show="false" 
                                                data-toggle="new_form_settings"></span>
                                    </span>									
								</h2><hr>
							</td>
						</tr>
						<tr>
							<td>
								<div id="new_form_settings">
									<div id="form_details">';

                                        include $controller . 'forms/'.$formName . '.php';
									
echo                                '</div>
								</div>
							</td>
						</tr>
					</table>
				</div>';