<template>
	<el-dialog ref="dialog" custom-class="bg-wh tx-c box-shadow-none">
		<img :src="url">
	</el-dialog>
</template>
<style>
.box-shadow-none {
	box-shadow: 0 1px 3px rgba(0,0,0,0);
}

.box-shadow-none img {
	max-width:350px;
	max-height:200px;
  width:100%;
}
</style>
<script>
  export default {
    props: ['url'],
    data() {
      return {

      }
    },
    methods: {
      open() {
        this.$refs.dialog.open()
      }
    }
  }
</script>