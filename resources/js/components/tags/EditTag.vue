<template>
    <b-modal ref="my-modal" :title="modal_title" @hidden="$emit('modalClose')" no-fade>
        <div slot="modal-footer">
             <b-button variant="secondary" @click="hideModal">{{  trans('messages.cancel') }}</b-button>
             <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isLoading">
                {{ trans('messages.save') }}
                <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
            </b-button>
        </div>
        <form ref="my-form" @submit.prevent="saveTag">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>{{ trans('messages.name') }}:</label>
                        <input
                            type="text"
                            class="form-control col-sm-12"
                            :class="{ 'is-invalid' :  errors.name }"
                            :disabled="tag.is_automated == 1"
                            v-model="name" required>
                        <div v-if="errors.name" class="invalid-feedback">
                            <template v-for="error_message in errors.name" >{{ error_message }}</template>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('messages.color') }}:</label>
                        <chrome-color-picker v-model="color"></chrome-color-picker>
                    </div>
                </div>
                <div class="col-sm-6 pl-0">
                    <div class="form-group">
                        <label>{{ trans('messages.icon') }}:</label>
                        <div v-if="icon">
                            <span :class="['selected_icon_box', 'fa', icon]"></span>
                            <span>{{ icon }}</span>
                            <div class="col-sm-12 my-1 mx-0 p-0">
                                <button type="button" @click.prevent="icon = ''" class="btn btn-sm btn-danger btn-block">{{ trans('messages.clear-selction') }}</button>
                            </div>
                        </div>
                        <p v-else>{{ trans('messages.no-icon-selected') }}</p>
                        <font-awesome-picker 
                            :box_placeholder="trans('messages.search-icon')"
                            :selected_icon="icon" 
                            @selectIcon="iconSelected"
                        ></font-awesome-picker>
                    </div>
                </div>
            </div>
            <button ref="dummy_submit" style="display:none;"></button>
        </form>
    </b-modal>
</template>

<script>
import { Chrome } from 'vue-color';
import FontAwesomePicker from '../FontAwesomePicker/FontAwesomePicker.vue';

import axios from 'axios';

export default {
    props: ['tag'],
    components: {
        'chrome-color-picker': Chrome,
        'font-awesome-picker': FontAwesomePicker,
    },
    data : function(){
        return {
            id: this.tag.id,
            name: this.tag.name,
            color: this.tag.color || 'black',
            icon: this.tag.icon,
            isLoading: false,
            errors: []
        };
    },
    computed: {
        modal_title: function(){
            return this.id ? this.trans('messages.edit-tag') : this.trans('messages.add-tag');
        }
    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        iconSelected: function(res){
            this.icon = res.className;
        },
        saveTag: function(){
            let vm = this;
            this.isLoading = true;
            let data = {
                id: this.id,
                name: this.name,
                color: typeof this.color == 'string' ? this.color : this.color.hex,
                icon: this.icon
            };
            axios.post(route('tags.save').url(), data)
            .then(res => {
                let data = res.data;
                if(this.id)
                {
                    vm.$eventBus.$emit('tagUpdated', data.tag);
                }
                else
                {
                    vm.$eventBus.$emit('tagCreated', data.tag);
                }
                vm.hideModal();
            }).catch(error => {
                if(error.response.status == 422)
                {
                    vm.errors = error.response.data.errors;
                    vm.isLoading = false;
                }
                else
                {
                    throw error;
                }
            });
        }
    },
    mounted(){
        this.showModal();
    }
}
</script>

<style scoped>
    .selected_icon_box {
        width: 40px;
        height: 40px;
        padding: 12px;
        margin: 0 12px 12px 0;
        text-align: center;
        border-radius: 3px;
        font-size: 14px;
        box-shadow: 0 0 0 1px #ddd;
        color: inherit;
    }
</style>
