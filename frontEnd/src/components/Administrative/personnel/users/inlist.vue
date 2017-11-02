<template>
	<div>
		<div class="m-b-20 ovf-hd">
			<div class="fl w-200 m-l-30">
				<el-input placeholder="请输入注册用户姓名" v-model="keywords">
					<el-button slot="append" icon="search" @click="search()"></el-button>
				</el-input>
			</div>
		</div>
		<el-table
		:data="tableData"
		style="width: 100%"
        @selection-change="selectItem">
			<el-table-column
			type="selection"
			width="40">
			</el-table-column>
			<el-table-column
			label="姓名"
			prop="realname"
			width="100">
			</el-table-column>
            <el-table-column
                    prop="trealname"
                    label="推广员"
                    width="100">
            </el-table-column>
            <el-table-column
                    prop="phone"
                    label="手机"
                    width="150">
            </el-table-column>
            <el-table-column
                    prop="idcard"
                    label="身份证号"
                    width="250">
            </el-table-column>
            <el-table-column
                    prop="bankcard"
                    label="银行卡号"
                    width="250">
            </el-table-column>
            <el-table-column
                    prop="bankphone"
                    label="预留手机"
                    width="150">
            </el-table-column>

			<!--<el-table-column-->
			<!--label="状态"-->
			<!--width="80">-->
        <!--<template scope="scope">-->
          <!--<div>-->
            <!--{{ scope.row.status | status }}-->
          <!--</div>-->
        <!--</template>-->
			<!--</el-table-column>-->
            <!--<el-table-column-->
                    <!--label="备注"-->
                    <!--prop="remark">-->
            <!--</el-table-column>-->
			<el-table-column
			label="操作"
			width="250">
        <template scope="scope">
          <div>
            <span>
              <router-link :to="{ name: 'Webindex', params: { tcode: scope.row.tcode  }}" class="btn-link el-button el-button--success is-plain" title="右键复制链接" target="_blank">
                  右键复制链接
              </router-link>
            </span>
            <span>
              <router-link :to="{ name: 'usersEdit', params: { id: scope.row.id }}" class="btn-link edit-btn">
            编辑
              </router-link>
            </span>
            <span>
              <el-button size="small" type="danger" @click="confirmDelete(scope.row)">删除</el-button>
            </span>
          </div>
        </template>
			</el-table-column>
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
            tuid: 0
          }
        }
        this.apiGet('admin/users', data).then((res) => {
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