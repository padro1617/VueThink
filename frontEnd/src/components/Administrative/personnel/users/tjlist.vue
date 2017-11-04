<template>
	<div>
		<div class="m-b-20 ovf-hd">
			<div class="fl" v-show="addShow">
				<a class="btn-link-large add-btn" target="_blank"  @click="JSONToCSVConvertor">
					<i class="el-icon-sold-out"></i>&nbsp;&nbsp;导出Excel数据
				</a>
			</div>
			<!-- <div class="fl w-200 m-l-30">
				<el-input placeholder="请输入推广员姓名" v-model="keywords">
					<el-button slot="append" icon="search" @click="search()"></el-button>
				</el-input>
			</div> -->
		</div>
		<el-table
		:data="tableData"
		style="width: 100%"
        @selection-change="selectItem">

			<!-- <el-table-column
			type="selection"
			width="40">
			</el-table-column> -->

			<el-table-column v-for="(value, key, index) in tableData[0]"
      v-bind:key="index"
			v-bind:label="key"
			v-bind:prop="key"
      >
			</el-table-column>
<!-- 
			<el-table-column
			label="姓名"
			prop="realname"
			width="150">
			</el-table-column>
            <el-table-column
                    prop="trealname"
                    label="推广员"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="tcode"
                    label="专属ID"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="phone"
                    label="手机"
                    width="150">
            </el-table-column> -->
			<!--<el-table-column-->
			<!--label="状态"-->
			<!--width="80">-->
        <!--<template scope="scope">-->
          <!--<div>-->
            <!--{{ scope.row.status | status }}-->
          <!--</div>-->
        <!--</template>-->
			<!--</el-table-column>-->
            <!-- <el-table-column
                    label="注册时间"
                    prop="create_time">
            </el-table-column> -->
		</el-table>
		<div class="pos-rel p-t-20">
			<!--<btnGroup :selectedData="multipleSelection" :type="'users'"></btnGroup>-->
			<div class="block pages">
				<el-pagination
				@current-change="handleCurrentChange"
				layout="prev, pager, next"
				:page-size="limit"
				:current-page="currentPage"
				:total="dataCount">
				</el-pagination>
			</div>
		</div>
	</div>
</template>

<script>
  import btnGroup from '../../../Common/btn-group.vue'
  import http from '../../../../assets/js/http'

  export default {
    data() {
      return {
        tableData: [],
        dataCount: null,
        currentPage: null,
        keywords: '',
        multipleSelection: [],
        limit: 15
      }
    },
    methods: {
      search() {
        router.push({ path: this.$route.path, query: { keywords: this.keywords, page: 1 }})
      },
      selectItem(val) {
        console.log(val)
        this.multipleSelection = val
      },
      handleCurrentChange(page) {
        router.push({ path: this.$route.path, query: { keywords: this.keywords, page: page }})
      },
      confirmDelete(item) {
        this.$confirm('确认删除该用户?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          _g.openGlobalLoading()
          this.apiDelete('admin/users/', item.id).then((res) => {
            _g.closeGlobalLoading()
            this.handelResponse(res, (data) => {
              _g.toastMsg('success', '删除成功')
              setTimeout(() => {
                _g.shallowRefresh(this.$route.name)
              }, 1500)
            })
          })
        }).catch(() => {
          // catch error
        })
      },
      getAllUsers() {
        this.loading = true
        const data = {
          params: {
            keywords: this.keywords,
            page: this.currentPage,
            limit: this.limit,
            tuid: -1
          }
        }
        this.apiGet('admin/base/export', data).then((res) => {
          console.log('res = ', _g.j2s(res))
          this.handelResponse(res, (data) => {
            this.tableData = data.list
            this.dataCount = data.dataCount
          })
        })
      },
      getCurrentPage() {
        let data = this.$route.query
        if (data) {
          if (data.page) {
            this.currentPage = parseInt(data.page)
          } else {
            this.currentPage = 1
          }
        }
      },
      getKeywords() {
        let data = this.$route.query
        if (data) {
          if (data.keywords) {
            this.keywords = data.keywords
          } else {
            this.keywords = ''
          }
        }
      },
      init() {
        this.getKeywords()
        this.getCurrentPage()
        this.getAllUsers()
      },
      JSONToCSVConvertor() {
        let _src = 'http://' + window.location.host + '/tjexport.php'
        console.log(_src)
        let iframe
        try {
          iframe = document.createElement('<iframe name="fileUploaderEmptyHole">')
        } catch (ex) {
          iframe = document.createElement('iframe')
        }
        iframe.id = 'fileUploaderEmptyHole'
        iframe.name = 'fileUploaderEmptyHole'
        iframe.width = 0
        iframe.height = 0
        iframe.marginHeight = 0
        iframe.marginWidth = 0
        iframe.src = _src
        document.body.appendChild(iframe)
        setTimeout(function() {
         document.getElementById('fileUploaderEmptyHole').remove()
        }, 1000);
      }
    },
    created() {
      this.init()
    },
    computed: {
      addShow() {
        return _g.getHasRule('users-save')
      },
      editShow() {
        return _g.getHasRule('users-update')
      },
      deleteShow() {
        return _g.getHasRule('users-delete')
      }
    },
    watch: {
      '$route' (to, from) {
        this.init()
      }
    },
    components: {
      btnGroup
    },
    mixins: [http]
  }
</script>