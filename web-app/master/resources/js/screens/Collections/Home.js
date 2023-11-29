import React, {Suspense} from 'react'
import moment from 'moment-timezone'
import CollectionsLayout from 'layouts/Collections'

const HomeComponent = React.lazy( () => import('components/Collections/Home'))

import { Typography } from 'antd'
import { DashboardOutlined } from '@ant-design/icons' 

export default function Page(props) {
    return (
        <CollectionsLayout {...props}>
            <div className="fadeIn">
                    <Typography.Title level={2}><DashboardOutlined className="mr-2"/> Dashboard</Typography.Title>
                    <Typography.Text>Today is {moment().format('dddd, MMMM D, YYYY')}</Typography.Text>
                    <HomeComponent/>
            </div>
        </CollectionsLayout>
    )
}