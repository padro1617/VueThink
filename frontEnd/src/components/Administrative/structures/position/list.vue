<template>
	<div>
		<div class="m-b-20">
			<router-link class="btn-link-large add-btn" to="add">
				<i class="el-icon-plus"></i>&nbsp;&nbsp;添加借款平台
			</router-link>
		</div>
		<el-table
		:data="tableData"
		style="width: 100%"
		@selection-change="selectItem">
			<el-table-column
			type="selection"
			width="50">
			</el-table-column>
			<el-table-column
			label="平台名称"
			prop="name" width="195">
			</el-table-column>
            <el-table-column
                    label="预估额度"
                    prop="maxmoney" 
                    width="95">
            </el-table-column>
            <el-table-column
                    label="放贷天数"
                    prop="fddays"
                    width="95">
            </el-table-column>
            <el-table-column
                    label="日费率"
                    prop="flday"
                    width="95">
            </el-table-column>
            <el-table-column
                    label="贷款期限"
                    prop="limitday"
                    width="95">
            </el-table-column>
            <el-table-column
                    label="链接地址"
                    prop="squrl">
            </el-table-column>
			<!--<el-table-column
			label="状态"
      prop="status"
			width="100">
        <template scope="scope">
          <div>
            {{ scope.row.status | status }}
          </div>
        </template>
			</el-table-column>
            <el-table-column
                    label="备注"
                    prop="remark">
            </el-table-column>-->
			<el-table-column
			label="操作"
			width="200">
        <template scope="scope">
  				<div>
  					<span>
  						<router-link :to="{ name: 'positionEdit', params: { id: scope.row.id }}" class="btn-link edit-btn">
  						编辑
  						</router-link>
  					</span>
  					<span>
  						<el-button
  						size="small"
  						type="danger"
  						@click="confirmDelete(scope.row)">
  						删除
  						</el-button>
  					</span>
  				</div>
        </template>
			</el-table-column>
		</el-table>
		<!--<div class="pos-rel p-t-20">
			<btnGroup :selectedData="multipleSelection" :type="'posts'"></btnGroup>
		</div>-->
	</div>
</template>

<script>
  import btnGroup from '../../../Common/btn-group.vue'
  import http from '../../../../assets/js/http'

  export default {
    data() {
      return {
        tableData: [],
        multipleSelection: []
      }
    },
    methods: {
      selectItem(val) {
        this.multipleSelection = val
      },
      confirmDelete(item) {
        this.$confirm('确认删除该平台信息?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          _g.openGlobalLoading()
          this.apiDelete('admin/posts/', item.id).then((res) => {
            _g.closeGlobalLoading()
            this.handelResponse(res, (data) => {
              _g.toastMsg('success', '删除成功')
              setTimeout(() => {
                _g.shallowRefresh(this.$route.name)
              }, 1500)
            })
          })
        }).catch(() => {
        })
      },
      getPositions() {
        this.apiGet('admin/posts').then((res) => {
          this.handelResponse(res, (data) => {
            this.tableData = data
          })
        })
      }
    },
    created() {
      this.getPositions()
    },
    computed: {
      addShow() {
        return _g.getHasRule('posts-save')
      },
      editShow() {
        return _g.getHasRule('posts-update')
      },
      deleteShow() {
        return _g.getHasRule('posts-delete')
      }
    },
    components: {
      btnGroup
    },
    mixins: [http]
  }
</script>