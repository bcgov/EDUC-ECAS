<template>

    <div class="card">
        <div class="card-header">
            <button @click="displayEditModal" class="float-right btn btn-primary">Edit</button>
            <h2>
                <span v-if="data.preferred_first_name">{{ data.preferred_first_name }}</span>
                <span v-else>{{ data.first_name }}</span>
                {{ data.last_name }}
            </h2>
        </div>
        <div class="card-body">
            <p v-show=" ! new_user">{{ data.email }}<br/>
                {{ data.address_1 }}<br/>
                {{ data.city }}, {{ data.region }} {{ data.postal_code }}
            </p>
            <p v-if="data.professional_certificate_bc">
                <strong>BC Professional Certificate:</strong> {{ data.professional_certificate_bc
                }}
            </p>
            <p v-if="data.professional_certificate_yk">
                <strong>Yukon Professional Certificate:</strong> {{
                data.professional_certificate_yk }}</p>
            <p v-if="data.professional_certificate_other">
                <strong>Other Certificate:</strong> {{ data.professional_certificate_other }}</p>
            <p v-if="data.district">
                <strong>District:</strong> {{ data.district.name }}</p>
            <p v-if="data.school">
                <strong>School:</strong> {{ data.school.name }}</p>
        </div>

    </div>

</template>

<script>

    export default {
        name: "EcasProfile",

        props: {
            data: {},
            new_user: false,
        },

        mounted() {
            console.log('Ecas Profile Mounted');
        },

        methods: {
            displayEditModal() {
                console.log('edit request clicked');
                return this.$emit('edit-profile-request' )
            }
        }


    }
</script>