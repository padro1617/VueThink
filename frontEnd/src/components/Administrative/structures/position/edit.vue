<template>
  <div class="m-l-50 m-t-30 w-900">
    <!-- <div style="color:gray;font-size:14px;">
      <ul>
        <li style="list-style: initial;">上传logo使用淘宝旗下的免费图片存储平台</li>
        <li style="list-style: initial;">淘宝图片存储具备CDN加速，图片访问更快</li>
      </ul>
      <ul>
        <li style="list-style: initial;">用淘宝账号登陆后新建一个命名空间，</li>
        <li style="list-style: initial;">点击上传按钮，可上传图片</li>
        <li style="list-style: initial;">复制访问链接地址填写到logo输入框中</li>
      </ul>
    </div> -->
    <el-form ref="form" :model="form" :rules="rules" label-width="130px">
      <el-form-item label="平台名称" prop="name">
        <el-input v-model.trim="form.name" class="h-40 w-200"></el-input>
      </el-form-item>
            <el-form-item label="logo图片">
              <el-upload
              :action="uploadUrl"
              type="drag"
              limit="1"
              :on-exceed="onexceed"
              :thumbnail-mode="true"
              :on-remove="handleRemove"
              :on-success="uploadSuccess"
              :on-error="uploadFail"
              :default-file-list="fileList">
                <i class="el-icon-upload"></i>
                <div class="el-dragger__text">将文件拖到此处，或<em>点击上传</em></div>
                <div class="el-upload__tip" slot="tip">只能上传jpg/png文件</div>
              </el-upload>
            </el-form-item>
            
      <el-form-item label="" prop="logo">
        <el-input v-model.trim="form.logo" class="h-40 w-200" placeholder="将文件拖到此处上方可上传"></el-input>
        <!-- <a href="http://wantu.taobao.com/mediaportal/index.htm" target="_blank" class="btn-link el-button el-button--success">上传图片 注册后点击[立即使用]</a> -->
      </el-form-item>
            
      <el-form-item label="预估额度" prop="maxmoney">
        <el-input v-model.trim="form.maxmoney" class="h-40 w-200"></el-input>
      </el-form-item>
      <el-form-item label="放贷小时" prop="fddays">
        <el-input v-model.trim="form.fddays" class="h-40 w-200"></el-input>
      </el-form-item>
      <el-form-item label="日利息" prop="flday">
        <el-input v-model.trim="form.flday" class="h-40 w-200"></el-input>
      </el-form-item>
      <el-form-item label="贷款期限" prop="limitday">
        <el-input v-model.trim="form.limitday" class="h-40 w-200"></el-input>
      </el-form-item>
      <el-form-item label="链接地址" prop="squrl">
        <el-input v-model.trim="form.squrl" class="h-40 w-200" placeholder="http://xxx"></el-input>
      </el-form-item>
      <el-form-item label="备注">
        <el-input
          type="textarea"
          class="w-200"
          :autosize="{ minRows: 2, maxRows: 4}"
          placeholder="请输入内容"
          v-model="form.remark">
        </el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="edit('form')" :loading="isLoading">提交</el-button>
        <el-button @click="goback()">返回</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
  import http from '../../../../assets/js/http'
  import fomrMixin from '../../../../assets/js/form_com'
  import preview from '../../system/config/preview.vue'

  export default {
    data() {
      return {
        isLoading: false,
        fileList: [],
        propsImg: '',
        uploadUrl: '',
        form: {
          name: '',
          logo: '',
          fddays: 0,
          flday: '0.5',
          maxmoney: 0,
          limitday: '',
          squrl: '',
          remark: ''
        },
        rules: {
          name: [
            { required: true, message: '请输入平台名称', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      edit(form) {
        this.$refs[form].validate((valid) => {
          if (valid) {
            this.isLoading = !this.isLoading
            this.apiPut('admin/posts/', this.form.id, this.form).then((res) => {
              this.handelResponse(res, (data) => {
                _g.toastMsg('success', '编辑成功')
                setTimeout(() => {
                  this.goback()
                }, 1500)
              }, () => {
                this.isLoading = !this.isLoading
              })
            })
          }
        })
      },
      getPosInfo() {
        this.form.id = this.$route.params.id
        this.apiGet('admin/posts/' + this.form.id).then((res) => {
          this.handelResponse(res, (data) => {
            this.form.name = data.name
            this.form.remark = data.remark
            this.form.logo = data.logo
            this.form.fddays = data.fddays
            this.form.flday = data.flday
            this.form.maxmoney = data.maxmoney
            this.form.limitday = data.limitday
            this.form.squrl = data.squrl
            if (data.logo) {
              let img = window.HOST + data.logo
              if(data.logo.indexOf('http')>-1){
                img = data.logo
              }
              this.fileList[0] = { name: '图片', url: img ,status: 'finished'}
              //this.viewPic();
              // this.fileList.push({ name: '图片', url: img })
              this.form.logo = img
            }
          })
        })
      },
      uploadSuccess(res, file, fileList) {
        this.form.logo = res.data
        let data = {
          name: '图片',
          url: window.HOST + res.data
        }
        if (this.fileList.length) {
          this.fileList[0] = data
        } else {
          this.fileList.push(data)
        }
      },
      onexceed(){
        _g.toastMsg('warning','只可上传一张图片');
      },
      uploadFail(err, res, file) {
        console.log('err = ', _g.j2s(err))
        console.log('res = ', _g.j2s(res))
      },
      handleRemove(file, fileList) {
        console.log('file = ', file)
        console.log('fileList = ', fileList)
        this.form.logo = ''
      },
      viewPic() {
        this.propsImg = this.fileList[0].url
        this.$refs.preview.open()
      }
    },
    created() {
      this.uploadUrl = window.HOST + '/Upload'
      this.getPosInfo()
     
    },
    components: {
      preview
    },
    mixins: [http, fomrMixin]
  }
</script>