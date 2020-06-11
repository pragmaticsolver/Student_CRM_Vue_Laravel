<template>
    <div class="row"> 
        <!-- Tags -->
        <div class="col-md-6 col-sm-12">
            <div class="row">
                <div>
                    <h1 class="d-inline-block align-middle">{{ trans('messages.tags') }}</h1>
                    <div class="d-inline-block align-middle">
                        <button class="btn btn-primary ml-2" @click="edit_tag = { color: 'black'}">{{ trans('messages.add') }}</button>
                    </div>
                </div>
            </div>
            <!-- Add / Edit Tag -->
            <app-edit-tag v-if="edit_tag" :tag="edit_tag" @modalClose="edit_tag = null"></app-edit-tag>

            <!-- Tags List -->
            <div class="row">
                <app-tag v-for="tag in normal_tags" v-bind:key="tag.id" :tag="tag" @edit="edit_tag = tag"></app-tag>
            </div>
            <div class="row" v-if="isLoading">
                <b-spinner label="Spinning" class="preloader"></b-spinner>
            </div>
            <div class="row" v-if="!isLoading && normal_tags.length == 0">
                <div class="col-sm-12">
                    <p class="text-center">{{ trans('messages.no-records-found') }}</p>
                </div>
            </div>
        </div>

        
        <div class="col-md-6 col-sm-12">

            <!-- Automated Tags -->
            <div class="row">
                <h1>{{ trans('messages.automated-tags') }}</h1>
            </div>
             <!-- Tags List -->
            <div class="row">
                <app-tag v-for="tag in automated_tags" v-bind:key="tag.id" :tag="tag" @edit="edit_tag = tag"></app-tag>
            </div>
            <div class="row" v-if="isLoading">
                <b-spinner label="Spinning" class="preloader"></b-spinner>
            </div>
            <div class="row" v-if="!isLoading && automated_tags.length == 0">
                <div class="col-sm-12">
                    <p class="text-center">{{ trans('messages.no-records-found') }}</p>
                </div>
            </div>

            <!-- Tag Settings -->
            <div class="row mt-3">
                <app-tag-settings></app-tag-settings>
            </div>
        </div>
    </div>
</template>

<script>

import Tag from './Tag.vue';
import EditTag from './EditTag.vue';
import TagSettings from './TagSettings.vue';
import axios from 'axios';

export default {
    components: {
        'app-tag': Tag,
        'app-edit-tag': EditTag,
        'app-tag-settings': TagSettings
    },
    data: function(){
        return {
            tags: [],
            edit_tag: null,
            isLoading: false,
        }
    },
    computed: {
        normal_tags: function(){
            return this.tags.filter(tag => {
                return tag.is_automated == 0
            });
        },
        automated_tags: function(){
            return this.tags.filter(tag => {
                return tag.is_automated == 1
            });
        }
    },
    created: function(){
        this.$eventBus.$on('tagUpdated', (updatedTag) => {
            this.showMessage('success',trans('messages.tag-updated-successfully'));
            var index = this.tags.findIndex(tag => tag.id == updatedTag.id);
            this.tags.splice(index, 1, updatedTag); // Can not directly update value by this.tags[index] = updatedTag
        });
        this.$eventBus.$on('tagCreated', (createdTag) => {
            this.showMessage('success', trans('messages.tag-created-successfully'));
            this.tags.push(createdTag);
        });
        this.$eventBus.$on('tagDeleted', (tagId) => {
            this.showMessage('success', trans('messages.tag-deleted-successfully'));
            var index = this.tags.findIndex(tag => tag.id == tagId);
            this.tags.splice(index, 1);
        });
        this.fetchRecords();
    },
    methods: {
        fetchRecords: function(){
            this.isLoading = true;
            axios.get(route('tags.records').url())
                .then(res => {
                    this.isLoading = false;
                    let data = res.data;
                    this.tags = data;
                });
        }
    }
}
</script>

<style scoped>
    .preloader {
        margin: auto;
    }
</style>
