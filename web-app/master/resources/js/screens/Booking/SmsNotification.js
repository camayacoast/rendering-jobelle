import React from 'react'

import BookingLayout from 'layouts/Booking'

import { Route, NavLink, Switch } from 'react-router-dom'

import PageNotFound from 'common/PageNotFound'

import { Typography, Row, Col, Menu } from 'antd'
import { HomeOutlined, BookOutlined } from '@ant-design/icons'

const SmsNotification = React.lazy( () => import('components/Booking/SmsNotification'))
const EventManagament = React.lazy( () => import('components/Booking/SmsNotification/EventManagament'))

export default function Page(props) {
    return (
        <BookingLayout {...props}>
            <div className="fadeIn">
                <Typography.Title level={2}>SMS</Typography.Title>

                <Row gutter={[32,0]}>
                        <Col xl={5} xs={24}>
                            {/* <Typography.Text type="secondary">Navigation</Typography.Text> */}
                            <Menu
                                className="mb-4"
                                style={{border: '0', background: 'none'}}
                                theme="light"
                                mode="vertical"
                                selectedKeys={[props.location.pathname]}
                            >
                                <Menu.Item className="rounded-12" key="/booking/sms">
                                    <NavLink to="/booking/sms">Dashboard</NavLink>
                                </Menu.Item>
                                <Menu.Item className="rounded-12" key="/booking/sms/events">
                                    <NavLink to="/booking/sms/events">Events</NavLink>
                                </Menu.Item>
                            </Menu>
                        </Col>
                        <Col xl={19} xs={24}>
                            <Switch>
                                <Route path={`/booking/sms`} exact render={ () => <SmsNotification/> }/>
                                <Route path={`/booking/sms/events`} exact render={ () => <EventManagament/> }/>
                                <Route render={ () =>  <PageNotFound /> } />
                                {/* <Redirect to="/hoa"/> */}
                            </Switch>
                        </Col>
                    </Row>
            </div>
        </BookingLayout>
    )
}