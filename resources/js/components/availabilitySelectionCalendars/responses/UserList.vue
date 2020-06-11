<template>
    <b-modal ref="my-modal" :title="trans('messages.assessment-users')" @hidden="$emit('modalClose')" no-fade>
        <div slot="modal-footer">
            <b-button variant="secondary" @click="hideModal">{{  trans('messages.close') }}</b-button>
        </div>
         <table v-if="users.length > 0" class="table table-hover table-bordered">
            <thead>
                    <tr>
                    <th>{{ trans('messages.assessment-by') }}</th>
                    <th>{{ trans('messages.level-s') }}</th>
                    <th>{{ trans('messages.assessment') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(user,i) in users" :key="i">
                    <td>
                        <a :href="user.profile_url" target="_blank">{{ user.name }}</a>
                        <div>{{ user.type }}</div>
                    </td>
                    <td>
                        {{ user.levels }}
                    </td>
                    <td>
                        <a :href="user.view_assessment_url" target="_blank">{{ user.assessment_name }}</a>
                    </td>
                </tr>
           </tbody>
        </table>
        <p v-else class="text-center">
            {{ trans('messages.no-records-found') }}
        </p>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    props: ['users'],
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        }
    },
    mounted(){
        this.showModal();
    }
}
</script>
