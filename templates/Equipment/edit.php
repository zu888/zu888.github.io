<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Equipment $equipment
 */
?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js" integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    .document_auth_box{
        margin-bottom:40px;
    }
    .document_auth_box_top{
        display:flex;
    }
    .document_auth_box_top div{
        margin-right:20px;

    }
    .document_auth_box_top div input{
    }
    .document_auth_box_select label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
    }
    .document_auth_box_input{

    }
    .document_auth_box_select{
    }
    .checkbox-box{
        display:flex
    }
    .checkbox-box div{
        margin-right:12px;
    }
</style>

<div class="row content">
    <div class="column-responsive column-80" style="padding-left: 2.5%; padding-right: 2.5%; max-width: 100%">
    <div class="column-responsive column-80">
        <div class="equipment form content">
            <?= $this->Form->create($equipment, ['type' => 'file', 'id' => 'document-form']) ?>
            <fieldset>
                <legend><?= __('Edit Equipment') ?></legend>
                <?php

                    echo $this->Form->control('name');
                echo $this->Form->control('description');

                    echo $this->Form->control('hired_from_date', ['empty' => true]);
                    echo $this->Form->control('hired_until_date', ['empty' => true]);
                echo $this->Form->label('imagelabel', 'Image (Only JPEG, PNG and GIF image types are accepted)');
                echo '<br>';
                echo $this->Form->control('combined_file', [
                    'type' => 'file', // Use a single file input for both the file and the image
                    'label' => false,
                    'accept' => 'image/jpeg, image/png, image/gif, image/jpg',
                ]);

                // Add a hidden input field to store the image name
                echo $this->Form->hidden('image', [
                    'id' => 'image-hidden',
                    'value' => '', // This will be populated using JavaScript
                ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['style' => 'float: right;']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

    <script>
        jQuery(function () {
            jQuery('#hired-from-date').datetimepicker({
                onShow: function (ct) {
                    this.setOptions({
                        maxDate: jQuery('#hired-until-date').val() ? jQuery('#hired-until-date').val() : false,
                        format:'Y-m-d'
                    })
                },
                timepicker: false
            });
            jQuery('#hired-until-date').datetimepicker({
                onShow: function (ct) {
                    this.setOptions({
                        minDate: jQuery('#hired-from-date').val() ? jQuery('#hired-from-date').val() : false,
                        format:'Y-m-d'
                    })
                },
                timepicker: false
            });
        });
    </script>
