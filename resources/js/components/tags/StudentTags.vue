<template>
    <div>
        <b-spinner v-if="initializing" small label="Spinning" class="preloader m-1"></b-spinner>
        <template v-else>
            <div v-if="!edit" class="my-1">
                <span class="badge custom_tag"  v-b-tooltip.hover.top :title="tag.name"
                    v-for="tag in local_student_tags" :key="tag.id"
                    :style="{ 'background-color': tag.color }"
                    @click="toggleTagView"
                    >
                    <span v-show="display_expanded">{{ tag.name }}</span>
                    <span v-show="!display_expanded" :class="['fa', tag.icon ]"></span>
                </span>
                <span
                    v-if="enable_edit && !edit"
                    class="badge badge-primary custom_tag"
                    style="cursor:pointer;"
                    @click="edit = true"><span class="fa fa-plus"></span>
                </span>
            </div>
            <div v-if="edit" class="my-1">
                <div style="width:500px;">
                    <select2
                        v-model="local_selected_tag_ids"
                        :options="select2_options"
                        :settings="{ multiple: 'multiple', width: '100%', placeholder: trans('messages.choose-tags'), dropdownCssClass: 'student-tags-select2' }"
                    />
                </div>
                <button class="btn btn-sm btn-primary my-1" :disabled="saving"  @click="saveTags">{{ trans('messages.save') }}
                    <b-spinner v-if="saving" small label="Spinning" class="preloader"></b-spinner>
                </button>
                <button class="btn btn-sm btn-secondary my-1" :disabled="saving"  @click="edit = false">{{ trans('messages.cancel') }}</button>
            </div>
        </template>
    </div>
</template>

<script>
import Select2 from 'v-select2-component';
import _ from 'lodash';

export default {
    components: {
        'select2' : Select2
    },
    props: ['student_id','student_tags', 'enable_edit'],
    data: function(){
        return {
            'all_tags': [],
            'edit' : false,
            'initializing': true,
            'saving': false,
            local_student_tags: this.student_tags,
            local_selected_tag_ids: [],
        }
    },
    watch: {
        local_student_tags: function(){
            this.updateLocalSelectedTagIds();
        }
    },
    computed: {
        select2_options: function(){
            var temp = [];
            this.normal_tags.forEach(tag => {
                temp.push({ id: tag.id, text: tag.name})
            });
            return temp;
        },
        display_expanded: function(){
            return this.$eventBus.display_expanded_tags;
        },
        normal_tags: function(){
            return this.all_tags.filter(tag => {
                return tag.is_automated == 0
            });
        },
    },
    methods: {
        saveTags: function() {
            this.saving = true;
            let vm = this;
            axios.post(route('tags.save_student_tags').url(), {
                student_id: this.student_id,
                tag_ids: this.local_selected_tag_ids
            }).then((res) => {
                let data = res.data;
                if(data.status == 1)
                {
                    this.edit = false;
                    this.saving = false;
                    vm.$eventBus.$emit('studentTagsUpdated', vm.student_id, data.student_tags);
                    this.showMessage('success', trans('messages.tags-saved-successfully'));
                }
            });
        },
        studentTagsUpdated: function(student_id, student_tags){
            if(this.student_id == student_id)
            {
                this.local_student_tags = student_tags;
            }
        },
        toggleTagView: function(){
            this.$eventBus.$emit('updateTagView', !this.display_expanded);
        },
        updateLocalSelectedTagIds(){
            let temp = [];
            this.local_student_tags.forEach(function(tag){
                if(tag.is_automated == 0)
                {
                    temp.push(tag.id);
                }
            });
            return this.local_selected_tag_ids = _.cloneDeep(temp);
        }
    },
    created: function(){
        this.$eventBus.$on('allTags', (tags) => {
            this.all_tags = tags;
            this.initializing = false;
        });
        this.$eventBus.$on('studentTagsUpdated', (student_id, tag_ids) => {
            this.studentTagsUpdated(student_id, tag_ids)
        });
        this.$eventBus.$on('updateTagView', (status) => {
            this.$eventBus.display_expanded_tags = status;
        });
        this.$eventBus.fetchAllTags();
        this.updateLocalSelectedTagIds();
    }
}
</script>

<style scoped>
    .custom_tag {
        color:white;
        font-size:10px;
        padding: 5px;
        cursor: pointer;
        margin:2px;
        min-width: 20px;
    }
</style>


