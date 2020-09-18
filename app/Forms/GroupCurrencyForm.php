<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class GroupCurrencyForm extends Form
{
    public function buildForm()
    {
        // This is the configuration for the group currency form presented to users when submitting pieces to the gallery.
        // It can be configured to your liking, though this configuration is presented as an example.
        // The precise formula for group currency awards is more or less hardcoded; it takes values from this form
        // and performs basic operations (addition, multiplication) to arrive at the final number.
        // In an effort to make it accessible, the function that countains it has been placed in app/Helpers/AssetHelpers, at the top.

        // Form names shouldn't have spaces, only underscores (_) like these examples.
        // Configuration values are given in pairs of keys and values, arranged like so:
        // 'key' => 'value'
        // and sometimes in arrays, like this:
        // ['key1' => 'value1', 'key2' => 'value2']
        // For multiple choice questions, for example the one immediately below, the options 'art', 'lit', etc
        // are hidden to the user, and the values are displayed instead. Rather, the keys are sent to be processed when
        // the form is submitted. Using this, we can have options that are secretly numeric values for the currency formula,
        // but appear to the user as human-readable options!

        // Each of these has a key, 'label', which corresponds to the form's label as displayed.

        $this
        // Creates a form for selecting the type of piece being submitted,
        // where users can select multiple options (for instance, when submitting
        // literature with accompanying artwork)
        ->add('piece_type', 'choice', [
            'choices' => ['art' => 'Digital or Traditional Art', 'lit' => 'Writing and Poetry', 
            'craft' => 'Craft or Other Physical Item'],
            'choice_options' => ['wrapper' => ['class' => 'choice-wrapper'],
            'label_attr' => ['class' => 'label-class'],
            ],
            'label' => 'Piece Type (Select as many as apply)',
            'selected' => ['art'],
            'expanded' => true,
            'multiple' => true,
            'rules' => ['required'],
        ])

        // Creates a form in which the user selects one option.
        // While the above is just for the sake of information, starting here,
        // group currency values are used for the options internally.
        ->add('art_finish', 'choice', [
            'choices' => ['0.5' => 'Sketch', '1' => 'Clean Lines/Lineless', '3' => 'Painted'],
            'choice_options' => ['wrapper' => ['class' => 'choice-wrapper'],
            'label_attr' => ['class' => 'label-class'],
            ],
            'label' => 'Level of Finish (For Digital/Traditional Artwork)',
            'selected' => ['0'],
            'expanded' => true,
            'multiple' => false,
        ])
        // This will be added to the above
        ->add('art_type', 'choice', [
            'choices' => ['0' => 'Headshot', '1' => 'Bust', '2' => 'Full Body Chibi', '3' => 'Full Body'],
            'choice_options' => ['wrapper' => ['class' => 'choice-wrapper'],
            'label_attr' => ['class' => 'label-class'],
            ],
            'label' => 'Art Type (For Digital/Traditional Artwork)',
            'selected' => ['0'],
            'expanded' => true,
            'multiple' => false,
        ])
        // In this particular case, these will be multiplied with the above art_type and added to the total
        ->add('art_bonus', 'choice', [
            'choices' => ['1' => 'Colored', '1' => 'Shading', '1' => 'Background'],
            'choice_options' => ['wrapper' => ['class' => 'choice-wrapper'],
            'label_attr' => ['class' => 'label-class'],
            ],
            'label' => 'Bonus Options (Select as many as apply)',
            'selected' => ['art'],
            'expanded' => true,
            'multiple' => true,
        ])
        // This adds a checkbox which is unchecked by default, 
        // for selecting if the art is using a base/is a YCH.
        ->add('base', 'checkbox', [
            'label' => 'Base (P2U/F2U) or YCH',
            'value' => 1,
            'checked' => false,
        ])
        // Now we add a simple field for frame count for animattions.
        // In this case, there are no options needed; it's just a field for a number.
        ->add('frame_count', 'number', [
            'label' => 'Frame Count (For Animations)',
        ])
        
        // Now we add a field for word count for literature, much like the above.
        ->add('word_count', 'number', [
            'label' => 'Word Count (For Writing or Poetry)',
        ]);
    }
}
