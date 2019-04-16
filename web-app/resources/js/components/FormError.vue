<template>
    <span class="text-danger" v-if="errors[field]">
        {{ errors[field][0] }}
    </span>
</template>

<script>
    export default {
        props: ['errors', 'field'],
    }
</script>