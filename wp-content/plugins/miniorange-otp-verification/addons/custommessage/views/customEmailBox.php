<?php

echo '   <div id="custom_email_box">
            <table style="width:100%">
                <tr>
                    <td>
                        <b>'.mo_('From ID:').'</b>
                        <div >
                            <input  '.esc_attr($disabled).'
                                    id="custom_email_from_id"
                                    class="mo_registration_table_textbox"
                                    style="border:1px solid #ddd"
                                    name="fromEmail"
                                    placeholder="'.mo_("Enter email address").'"
                                    value = ""
                                    required/>
                        </div><br>
                        <b>'.mo_('From Name:').'</b>
                        <div >
                            <input  '.esc_attr($disabled).'
                                    id="custom_email_from_name"
                                    class="mo_registration_table_textbox"
                                    style="border:1px solid #ddd"
                                    name="fromName"
                                    placeholder="'.mo_("Enter Name").'"
                                    value = ""
                                    required/>
                        </div><br>
                        <b>'.mo_('Subject:').'</b>
                        <div >
                            <input  '.esc_attr($disabled).'
                                    id="custom_email_subject"
                                    class="mo_registration_table_textbox"
                                    style="border:1px solid #ddd"
                                    name="subject"
                                    placeholder="'.mo_("Enter your OTP Email Subject").'"
                                    value = ""
                                    required/>
                        </div><br>
                        <b>'.mo_('To Email Address:').'</b>
                        <div >
                            <input  '.esc_attr($disabled).'
                                    id="custom_email_to"
                                    class="mo_registration_table_textbox"
                                    style="border:1px solid #ddd"
                                    name="toEmail"
                                    placeholder="'.mo_("Enter semicolon (;) separated email-addresses
                                                        to send the email to").'"
                                    value = ""
                                    required/>
                        </div><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>'.mo_('Body:').'</b>';
                        wp_editor( $content, $editorId ,$templateSettings);
    echo'			</td>
                </tr>
            </table>
        </div>';