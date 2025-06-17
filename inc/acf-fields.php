<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
    'key' => 'group_contact_page',
    'title' => 'Contact Page Settings',
    'fields' => array(
        array(
            'key' => 'field_address',
            'label' => 'Address',
            'name' => 'address',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_phone',
            'label' => 'Phone',
            'name' => 'phone',
            'type' => 'text',
        ),
        array(
            'key' => 'field_email',
            'label' => 'Email',
            'name' => 'email',
            'type' => 'email',
        ),
        array(
            'key' => 'field_business_hours',
            'label' => 'Business Hours',
            'name' => 'business_hours',
            'type' => 'textarea',
            'rows' => 3,
        ),
        // Social Media Links
        array(
            'key' => 'field_instagram_url',
            'label' => 'Instagram URL',
            'name' => 'instagram_url',
            'type' => 'url',
        ),
        array(
            'key' => 'field_facebook_url',
            'label' => 'Facebook URL',
            'name' => 'facebook_url',
            'type' => 'url',
        ),
        array(
            'key' => 'field_twitter_url',
            'label' => 'Twitter URL',
            'name' => 'twitter_url',
            'type' => 'url',
        ),
        array(
            'key' => 'field_youtube_url',
            'label' => 'YouTube URL',
            'name' => 'youtube_url',
            'type' => 'url',
        ),
        // FAQs (6 pairs)
        array(
            'key' => 'field_faq_1_question',
            'label' => 'FAQ 1 Question',
            'name' => 'faq_1_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_1_answer',
            'label' => 'FAQ 1 Answer',
            'name' => 'faq_1_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_faq_2_question',
            'label' => 'FAQ 2 Question',
            'name' => 'faq_2_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_2_answer',
            'label' => 'FAQ 2 Answer',
            'name' => 'faq_2_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_faq_3_question',
            'label' => 'FAQ 3 Question',
            'name' => 'faq_3_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_3_answer',
            'label' => 'FAQ 3 Answer',
            'name' => 'faq_3_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_faq_4_question',
            'label' => 'FAQ 4 Question',
            'name' => 'faq_4_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_4_answer',
            'label' => 'FAQ 4 Answer',
            'name' => 'faq_4_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_faq_5_question',
            'label' => 'FAQ 5 Question',
            'name' => 'faq_5_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_5_answer',
            'label' => 'FAQ 5 Answer',
            'name' => 'faq_5_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
        array(
            'key' => 'field_faq_6_question',
            'label' => 'FAQ 6 Question',
            'name' => 'faq_6_question',
            'type' => 'text',
        ),
        array(
            'key' => 'field_faq_6_answer',
            'label' => 'FAQ 6 Answer',
            'name' => 'faq_6_answer',
            'type' => 'textarea',
            'rows' => 3,
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_template',
                'operator' => '==',
                'value' => 'templates/contact.php',
            ),
        ),
    ),
));

acf_add_local_field_group(array(
    'key' => 'group_about_page',
    'title' => 'About Page Fields',
    'fields' => array(
        array(
            'key' => 'field_about_hero_title',
            'label' => 'Hero Title',
            'name' => 'hero_title',
            'type' => 'text',
            'instructions' => 'Enter the main title for the About page',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_hero_description',
            'label' => 'Hero Description',
            'name' => 'hero_description',
            'type' => 'textarea',
            'instructions' => 'Enter the description text for the About page',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_story_image',
            'label' => 'Our Story Image',
            'name' => 'story_image',
            'type' => 'image',
            'instructions' => 'Upload the image for the Our Story section',
            'required' => 1,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_about_story_title',
            'label' => 'Our Story Title',
            'name' => 'story_title',
            'type' => 'text',
            'instructions' => 'Enter the title for the Our Story section',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_story_content',
            'label' => 'Our Story Content',
            'name' => 'story_content',
            'type' => 'wysiwyg',
            'instructions' => 'Enter the content for the Our Story section',
            'required' => 1,
            'tabs' => 'all',
            'toolbar' => 'full',
            'media_upload' => 1,
        ),
        array(
            'key' => 'field_about_values_title',
            'label' => 'Values Section Title',
            'name' => 'values_title',
            'type' => 'text',
            'instructions' => 'Enter the title for the Values section',
            'required' => 1,
        ),
        // Value 1
        array(
            'key' => 'field_value_1_title',
            'label' => 'Value 1 Title',
            'name' => 'value_1_title',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_1_description',
            'label' => 'Value 1 Description',
            'name' => 'value_1_description',
            'type' => 'textarea',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_1_icon',
            'label' => 'Value 1 Icon',
            'name' => 'value_1_icon',
            'type' => 'text',
            'instructions' => 'Enter the SVG icon code for this value',
            'required' => 1,
        ),
        // Value 2
        array(
            'key' => 'field_value_2_title',
            'label' => 'Value 2 Title',
            'name' => 'value_2_title',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_2_description',
            'label' => 'Value 2 Description',
            'name' => 'value_2_description',
            'type' => 'textarea',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_2_icon',
            'label' => 'Value 2 Icon',
            'name' => 'value_2_icon',
            'type' => 'text',
            'instructions' => 'Enter the SVG icon code for this value',
            'required' => 1,
        ),
        // Value 3
        array(
            'key' => 'field_value_3_title',
            'label' => 'Value 3 Title',
            'name' => 'value_3_title',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_3_description',
            'label' => 'Value 3 Description',
            'name' => 'value_3_description',
            'type' => 'textarea',
            'required' => 1,
        ),
        array(
            'key' => 'field_value_3_icon',
            'label' => 'Value 3 Icon',
            'name' => 'value_3_icon',
            'type' => 'text',
            'instructions' => 'Enter the SVG icon code for this value',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_team_title',
            'label' => 'Team Section Title',
            'name' => 'team_title',
            'type' => 'text',
            'instructions' => 'Enter the title for the Team section',
            'required' => 1,
        ),
        // Team Member 1
        array(
            'key' => 'field_team_member_1_image',
            'label' => 'Team Member 1 Image',
            'name' => 'team_member_1_image',
            'type' => 'image',
            'required' => 1,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_team_member_1_name',
            'label' => 'Team Member 1 Name',
            'name' => 'team_member_1_name',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_team_member_1_position',
            'label' => 'Team Member 1 Position',
            'name' => 'team_member_1_position',
            'type' => 'text',
            'required' => 1,
        ),
        // Team Member 2
        array(
            'key' => 'field_team_member_2_image',
            'label' => 'Team Member 2 Image',
            'name' => 'team_member_2_image',
            'type' => 'image',
            'required' => 1,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_team_member_2_name',
            'label' => 'Team Member 2 Name',
            'name' => 'team_member_2_name',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_team_member_2_position',
            'label' => 'Team Member 2 Position',
            'name' => 'team_member_2_position',
            'type' => 'text',
            'required' => 1,
        ),
        // Team Member 3
        array(
            'key' => 'field_team_member_3_image',
            'label' => 'Team Member 3 Image',
            'name' => 'team_member_3_image',
            'type' => 'image',
            'required' => 1,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_team_member_3_name',
            'label' => 'Team Member 3 Name',
            'name' => 'team_member_3_name',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_team_member_3_position',
            'label' => 'Team Member 3 Position',
            'name' => 'team_member_3_position',
            'type' => 'text',
            'required' => 1,
        ),
        // Team Member 4
        array(
            'key' => 'field_team_member_4_image',
            'label' => 'Team Member 4 Image',
            'name' => 'team_member_4_image',
            'type' => 'image',
            'required' => 1,
            'return_format' => 'array',
            'preview_size' => 'medium',
            'library' => 'all',
        ),
        array(
            'key' => 'field_team_member_4_name',
            'label' => 'Team Member 4 Name',
            'name' => 'team_member_4_name',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_team_member_4_position',
            'label' => 'Team Member 4 Position',
            'name' => 'team_member_4_position',
            'type' => 'text',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_cta_title',
            'label' => 'CTA Title',
            'name' => 'cta_title',
            'type' => 'text',
            'instructions' => 'Enter the title for the CTA section',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_cta_description',
            'label' => 'CTA Description',
            'name' => 'cta_description',
            'type' => 'textarea',
            'instructions' => 'Enter the description for the CTA section',
            'required' => 1,
        ),
        array(
            'key' => 'field_about_cta_button_1',
            'label' => 'CTA Button 1',
            'name' => 'cta_button_1',
            'type' => 'group',
            'instructions' => 'First button for CTA section',
            'required' => 1,
            'sub_fields' => array(
                array(
                    'key' => 'field_about_cta_button_1_text',
                    'label' => 'Button 1 Text',
                    'name' => 'text',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_about_cta_button_1_url',
                    'label' => 'Button 1 URL',
                    'name' => 'url',
                    'type' => 'url',
                    'required' => 1,
                ),
            ),
        ),
        array(
            'key' => 'field_about_cta_button_2',
            'label' => 'CTA Button 2',
            'name' => 'cta_button_2',
            'type' => 'group',
            'instructions' => 'Second button for CTA section',
            'required' => 1,
            'sub_fields' => array(
                array(
                    'key' => 'field_about_cta_button_2_text',
                    'label' => 'Button 2 Text',
                    'name' => 'text',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_about_cta_button_2_url',
                    'label' => 'Button 2 URL',
                    'name' => 'url',
                    'type' => 'url',
                    'required' => 1,
                ),
            ),
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_template',
                'operator' => '==',
                'value' => 'page-about.php',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
));

acf_add_local_field_group(array(
    'key' => 'group_wholesale',
    'title' => 'Wholesale Page Settings',
    'fields' => array(
        array(
            'key' => 'field_wholesale_hero_title',
            'label' => 'Hero Title',
            'name' => 'hero_title',
            'type' => 'text',
            'default_value' => 'Become a Hype Pups Retailer'
        ),
        array(
            'key' => 'field_wholesale_hero_subtitle',
            'label' => 'Hero Subtitle',
            'name' => 'hero_subtitle',
            'type' => 'textarea',
            'default_value' => 'Join our network of premium retailers and bring the hottest dog streetwear brand to your customers.'
        ),
        array(
            'key' => 'field_wholesale_catalog_title',
            'label' => 'Catalog Section Title',
            'name' => 'catalog_title',
            'type' => 'text',
            'default_value' => 'Spring/Summer 2025 Collection'
        ),
        array(
            'key' => 'field_wholesale_catalog_description',
            'label' => 'Catalog Description',
            'name' => 'catalog_description',
            'type' => 'textarea',
            'default_value' => 'Our latest wholesale catalog features our complete product line with detailed specifications, pricing, and minimum order quantities.'
        ),
        array(
            'key' => 'field_wholesale_catalog_image',
            'label' => 'Catalog Preview Image',
            'name' => 'catalog_image',
            'type' => 'image',
            'return_format' => 'array'
        ),
        array(
            'key' => 'field_wholesale_catalog_features',
            'label' => 'Catalog Features',
            'name' => 'catalog_features',
            'type' => 'group',
            'sub_fields' => array(
                array(
                    'key' => 'field_catalog_feature_1',
                    'label' => 'Feature 1',
                    'name' => 'feature_1',
                    'type' => 'text',
                    'default_value' => 'Complete product specifications'
                ),
                array(
                    'key' => 'field_catalog_feature_2',
                    'label' => 'Feature 2',
                    'name' => 'feature_2',
                    'type' => 'text',
                    'default_value' => 'Wholesale pricing and MOQs'
                ),
                array(
                    'key' => 'field_catalog_feature_3',
                    'label' => 'Feature 3',
                    'name' => 'feature_3',
                    'type' => 'text',
                    'default_value' => 'High-resolution product images'
                ),
                array(
                    'key' => 'field_catalog_feature_4',
                    'label' => 'Feature 4',
                    'name' => 'feature_4',
                    'type' => 'text',
                    'default_value' => 'Sizing and material information'
                )
            )
        ),
        array(
            'key' => 'field_wholesale_benefits_title',
            'label' => 'Benefits Section Title',
            'name' => 'benefits_title',
            'type' => 'text',
            'default_value' => 'Why Partner With Us'
        ),
        array(
            'key' => 'field_wholesale_benefits',
            'label' => 'Benefits',
            'name' => 'benefits',
            'type' => 'group',
            'sub_fields' => array(
                array(
                    'key' => 'field_benefit_1_title',
                    'label' => 'Benefit 1 Title',
                    'name' => 'benefit_1_title',
                    'type' => 'text',
                    'default_value' => 'High-Quality Products'
                ),
                array(
                    'key' => 'field_benefit_1_description',
                    'label' => 'Benefit 1 Description',
                    'name' => 'benefit_1_description',
                    'type' => 'textarea',
                    'default_value' => 'Our premium materials and craftsmanship ensure customer satisfaction and repeat business.'
                ),
                array(
                    'key' => 'field_benefit_1_image',
                    'label' => 'Benefit 1 Image',
                    'name' => 'benefit_1_image',
                    'type' => 'image',
                    'return_format' => 'array'
                ),
                array(
                    'key' => 'field_benefit_2_title',
                    'label' => 'Benefit 2 Title',
                    'name' => 'benefit_2_title',
                    'type' => 'text',
                    'default_value' => 'Strong Brand Recognition'
                ),
                array(
                    'key' => 'field_benefit_2_description',
                    'label' => 'Benefit 2 Description',
                    'name' => 'benefit_2_description',
                    'type' => 'textarea',
                    'default_value' => 'Benefit from our growing social media presence and loyal customer base.'
                ),
                array(
                    'key' => 'field_benefit_2_image',
                    'label' => 'Benefit 2 Image',
                    'name' => 'benefit_2_image',
                    'type' => 'image',
                    'return_format' => 'array'
                ),
                array(
                    'key' => 'field_benefit_3_title',
                    'label' => 'Benefit 3 Title',
                    'name' => 'benefit_3_title',
                    'type' => 'text',
                    'default_value' => 'Competitive Margins'
                ),
                array(
                    'key' => 'field_benefit_3_description',
                    'label' => 'Benefit 3 Description',
                    'name' => 'benefit_3_description',
                    'type' => 'textarea',
                    'default_value' => 'Enjoy healthy profit margins that make Hype Pups a valuable addition to your inventory.'
                ),
                array(
                    'key' => 'field_benefit_3_image',
                    'label' => 'Benefit 3 Image',
                    'name' => 'benefit_3_image',
                    'type' => 'image',
                    'return_format' => 'array'
                )
            )
        ),
        array(
            'key' => 'field_wholesale_testimonials_title',
            'label' => 'Testimonials Section Title',
            'name' => 'testimonials_title',
            'type' => 'text',
            'default_value' => 'What Our Partners Say'
        ),
        array(
            'key' => 'field_wholesale_testimonials',
            'label' => 'Testimonials',
            'name' => 'testimonials',
            'type' => 'group',
            'sub_fields' => array(
                array(
                    'key' => 'field_testimonial_1_text',
                    'label' => 'Testimonial 1 Text',
                    'name' => 'testimonial_1_text',
                    'type' => 'textarea',
                    'default_value' => 'Since adding Hype Pups to our store, we\'ve seen a significant increase in foot traffic and sales. Their products are always the first to sell out!'
                ),
                array(
                    'key' => 'field_testimonial_1_name',
                    'label' => 'Testimonial 1 Name',
                    'name' => 'testimonial_1_name',
                    'type' => 'text',
                    'default_value' => 'Sarah Johnson'
                ),
                array(
                    'key' => 'field_testimonial_1_position',
                    'label' => 'Testimonial 1 Position',
                    'name' => 'testimonial_1_position',
                    'type' => 'text',
                    'default_value' => 'Owner, Urban Paws Boutique'
                ),
                array(
                    'key' => 'field_testimonial_1_image',
                    'label' => 'Testimonial 1 Image',
                    'name' => 'testimonial_1_image',
                    'type' => 'image',
                    'return_format' => 'array'
                )
            )
        ),
        array(
            'key' => 'field_wholesale_faq_title',
            'label' => 'FAQ Section Title',
            'name' => 'faq_title',
            'type' => 'text',
            'default_value' => 'Frequently Asked Questions'
        ),
        array(
            'key' => 'field_wholesale_faqs',
            'label' => 'FAQs',
            'name' => 'faqs',
            'type' => 'group',
            'sub_fields' => array(
                array(
                    'key' => 'field_faq_1_question',
                    'label' => 'FAQ 1 Question',
                    'name' => 'faq_1_question',
                    'type' => 'text',
                    'default_value' => 'What is the minimum order quantity?'
                ),
                array(
                    'key' => 'field_faq_1_answer',
                    'label' => 'FAQ 1 Answer',
                    'name' => 'faq_1_answer',
                    'type' => 'textarea',
                    'default_value' => 'Our minimum opening order is $1,000, with subsequent orders at $500 minimum. This ensures you have a good selection of products to showcase our brand.'
                ),
                array(
                    'key' => 'field_faq_2_question',
                    'label' => 'FAQ 2 Question',
                    'name' => 'faq_2_question',
                    'type' => 'text',
                    'default_value' => 'Do you offer exclusivity for my region?'
                ),
                array(
                    'key' => 'field_faq_2_answer',
                    'label' => 'FAQ 2 Answer',
                    'name' => 'faq_2_answer',
                    'type' => 'textarea',
                    'default_value' => 'We consider territorial exclusivity on a case-by-case basis. Please mention your interest in exclusivity when applying, and we\'ll discuss options during the review process.'
                ),
                array(
                    'key' => 'field_faq_3_question',
                    'label' => 'FAQ 3 Question',
                    'name' => 'faq_3_question',
                    'type' => 'text',
                    'default_value' => 'What are your payment terms?'
                ),
                array(
                    'key' => 'field_faq_3_answer',
                    'label' => 'FAQ 3 Answer',
                    'name' => 'faq_3_answer',
                    'type' => 'textarea',
                    'default_value' => 'We require prepayment for first orders. Established retailers may qualify for Net 30 terms after a history of timely payments.'
                ),
                array(
                    'key' => 'field_faq_4_question',
                    'label' => 'FAQ 4 Question',
                    'name' => 'faq_4_question',
                    'type' => 'text',
                    'default_value' => 'Can I return unsold merchandise?'
                ),
                array(
                    'key' => 'field_faq_4_answer',
                    'label' => 'FAQ 4 Answer',
                    'name' => 'faq_4_answer',
                    'type' => 'textarea',
                    'default_value' => 'We do not accept returns of unsold merchandise. We recommend starting with a carefully curated selection to minimize risk.'
                ),
                array(
                    'key' => 'field_faq_5_question',
                    'label' => 'FAQ 5 Question',
                    'name' => 'faq_5_question',
                    'type' => 'text',
                    'default_value' => 'Do you offer dropshipping for online retailers?'
                ),
                array(
                    'key' => 'field_faq_5_answer',
                    'label' => 'FAQ 5 Answer',
                    'name' => 'faq_5_answer',
                    'type' => 'textarea',
                    'default_value' => 'Yes, we offer dropshipping services for approved online retailers. Additional fees may apply, and minimum order requirements still apply.'
                ),
                array(
                    'key' => 'field_faq_6_question',
                    'label' => 'FAQ 6 Question',
                    'name' => 'faq_6_question',
                    'type' => 'text',
                    'default_value' => 'How often do you release new collections?'
                ),
                array(
                    'key' => 'field_faq_6_answer',
                    'label' => 'FAQ 6 Answer',
                    'name' => 'faq_6_answer',
                    'type' => 'textarea',
                    'default_value' => 'We release major seasonal collections four times a year, with limited edition drops and collaborations throughout the year.'
                )
            )
        )
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_template',
                'operator' => '==',
                'value' => 'template-wholesale.php'
            )
        )
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => ''
));

endif; 