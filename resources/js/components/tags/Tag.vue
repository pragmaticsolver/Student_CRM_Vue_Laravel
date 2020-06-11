<template>
    <div class="col-12">
        <div class="row">
            <div class="col-md-8 col-sm-8 p-2 my-1" :style="{ 'background-color' : tag.color, 'color': '#fff' , cursor: 'pointer' }">
                <span :class="['fa' , tag.icon ]"></span> {{ tag.name }}
            </div>
            <div class="col-md-4 col-sm-4">
                <b-button variant="primary" @click="$emit('edit')" :disabled="isDeleting">{{ trans('messages.edit') }}</b-button>
                <b-button variant="danger" v-if="tag.is_automated == 0" @click.prevent="deleteTag" :disabled="isDeleting">
                    {{ trans('messages.delete') }}
                    <b-spinner v-if="isDeleting" small label="Spinning"></b-spinner>
                </b-button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ['tag'],
    data: function(){
        return {
            isDeleting: false,
        }
    },
    methods: {
        deleteTag: function(){
            let vm = this;
            this.$swal.fire({
                title: trans('messages.are-you-sure'),
                text: trans('messages.you-wont-be-able-to-revert-this'),
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: trans('messages.cancel'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(function (result) {
                if (result.value) {
                    vm.isDeleting = true;
                    axios.delete(route('tags.delete', vm.tag.id).url())
                        .then(res => {
                            vm.$eventBus.$emit('tagDeleted', vm.tag.id);
                        });
                }
            });
        }
    }
}
</script>
