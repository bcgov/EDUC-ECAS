<template>
    <div class="card">
        <div class="card-header">
            <h2>My Contracts</h2>
        </div>
        <div class="card-body">
            <p>Download and submit your signed contracts for the current session.</p>
            <div class="my-contract-box mt-n3">
                <button class="btn btn-primary">
                    <span>
                        <div class="text-center">Manage my contracts</div>
                    </span>
                </button>
                <div class="status-box text-center mt-1">Action required</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Contracts",
}
</script>

<style scoped>
.my-contract-box {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-end;
}

.status-box {
    background-color: red;
    color: white;
    width: 150px;
    padding: 2px 4px;
}
</style>
