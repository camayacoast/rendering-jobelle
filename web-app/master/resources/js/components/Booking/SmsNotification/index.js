import React, { useEffect, useState } from 'react'
import moment from 'moment-timezone'
import Loading from 'common/Loading'

import SmsNotificationServices from 'services/Booking/SmsNotificationServices'
import RecentMessage from './RecentMessage'
import SmsLogs from './SmsLogs'

import { Row, Col, Card, Statistic, DatePicker, Table, Button, Input, message, Form, Space, Drawer, Select, Upload, Divider, Typography, Descriptions} from 'antd'
import { DownloadOutlined, SendOutlined, UploadOutlined, MinusCircleOutlined, PlusOutlined} from '@ant-design/icons'

function Page(props) {

  const [importedData, setImportData] = useState([]);

  const [selectedEvent, setSelectedEvent] = useState(null);

  const [openSendBulkDrawer, setOpenSendBulkDrawer] = useState(false);

  const [sendBulkMessageQuery, { isLoading: sendBulkMessageQueryLoading}] = SmsNotificationServices.sendBulkMessage();

  const [sendImportBulkMessageQuery, { isLoading: sendImportBulkMessageQueryLoading}] = SmsNotificationServices.sendImportBulkMessage();

  const {data, isLoading, refetch} = SmsNotificationServices.getMessages();

  const {data: smsAccountDetails, isLoading: smsAccountLoading, refetch: smsAccountRefetch} = SmsNotificationServices.getAccountDetails();

  const {data: smsLogs, isLoading: smsLogsLoading, refetch: smsLogsRefetch} = SmsNotificationServices.getSmsLogs();

  const [createSubject, { isLoading: createSubjectLoading}] = SmsNotificationServices.createSmsSubject();

  const {data: smsEvent, isLoading: smsEventLoading, refetch: smsEventRefetch} = SmsNotificationServices.getSmsEvent();

  const [bulkMessageForm] = Form.useForm();

  const [subjectForm] = Form.useForm();

  const events = [
    { label: 'Immediate', value: 'immediate' },
    { label: 'Schedule', value: 'schedule' },
  ];

  const { TextArea } = Input;

  const [selectedEventData, setSelectedEventData] = useState({});

  const handleSendBulkMessage = values => {

        if(sendBulkMessageQueryLoading) return false;

        const postData = {...values, ...selectedEventData, schedule_date: moment(values.schedule_date).format()};
        
        sendBulkMessageQuery(postData, {
        onSuccess: (res) => {

            if (selectedEvent === 'immediate') {

                message.success('Message successfully send!');

            } else if (selectedEvent === 'schedule') {

                message.success('Message successfully scheduled!');
            }

            refetch();
            smsAccountRefetch();
            smsLogsRefetch();
            bulkMessageForm.resetFields();
            setOpenSendBulkDrawer(false);
            setSelectedEvent(null);
            setSelectedEventData({});

        },
        onError: (e) => {
            message.error(e.message);
        }
    })
  }

  const handleImportBulkMessage = values => {

    if(sendImportBulkMessageQueryLoading) return false;

    if(!importedData.length) {
        return message.info("there's nothing to send!");
    }

     sendImportBulkMessageQuery({...values, data: importedData, schedule_date: moment(values.schedule_date).format()}, {
        onSuccess: (res) => {

            if (selectedEvent === 'immediate') {

                message.success('Message successfully send!');

            } else if (selectedEvent === 'schedule') {

                message.success('Message successfully scheduled!');
            }
            
            refetch();
            smsAccountRefetch();
            smsLogsRefetch();
            setOpenImportBulkDrawer(false);

        },
        onError: (e) => {
            message.error(e.message);
        }
    })
  }

 const getEvents = () => {

    if(smsEventLoading) return false;

    const events = smsEvent?.map(items => {
        return {
            label: items.event_name,
            value: items.id
        }
    })

    return events;
 }


 const handleCreateSubject = values => {

    if(createSubjectLoading) return false;

     createSubject({...values}, {
        onSuccess: (res) => {

            smsSubjectRefetch();
            subjectForm.resetFields();
        },
        onError: (e) => {
            message.error(e.message);
        }
    })
 }

 const handleEventChange = (value) => {
    setSelectedEvent(value);
 }

 const handleSmsEvents = value => {

    const filter = smsEvent.filter(item => item.id === value);

    setSelectedEventData({
        event_id: filter[0].id,
        event_name: filter[0].event_name,
        event_description: filter[0].event_description,
        message: filter[0].message,
        recepients: JSON.parse(filter[0].recepients)
    });

 }

 const dropDownFields = (menu) => {
    return (
         <>
            {menu}
            <Divider
                style={{
                margin: '8px 0',
                }}
            />
            <div
                style={{
                  padding: '0 8px 4px',
                }}
            >
                <Form form={subjectForm} onFinish={handleCreateSubject}>
                    <Row>
                        <Col md={15}>
                            <Form.Item name="subjectInput" rules={[{ required: true, message: 'subject is required' }]}>
                                <Input size="middle"/>
                            </Form.Item>
                        </Col>
                        <Col md={9}>
                            <Button type="text" className="mr-2" loading={createSubjectLoading} size="medium" htmlType="submit"><PlusOutlined/> Add Subject</Button>
                        </Col>
                    </Row>
                </Form>
            </div>
        </>
    );
 }

 const handleOpenBulkMessageDrawer = () => {
    setOpenSendBulkDrawer(true);
    console.log('open')
 }

 const handleCloseBulkMessageDrawer = () => {
    setOpenSendBulkDrawer(false);
    console.log('close')
 }

  return (
    <>
       <Typography.Title level={3}>Dashboard</Typography.Title>

       <Row gutter={[48,48]} className="mt-2 mb-4">
               <Col xl={6}>
                    <Card size="small" bordered={false} loading={smsAccountLoading} style={{borderLeft: 'solid 5px limegreen'}} className="card-shadow">
                        <Statistic title="Total credits" value={smsAccountDetails?.credit_balance} />
                    </Card>
                </Col>
                <Col xl={6}>
                    <Card size="small" bordered={false} loading={isLoading} style={{borderLeft: 'solid 5px limegreen'}} className="card-shadow">
                        <Statistic title="Total sent today" value={smsLogs?.countSentToday} />
                    </Card>
                </Col>
                <Col xl={6}>
                    <Card size="small" bordered={false} loading={isLoading} style={{borderLeft: 'solid 5px limegreen'}} className="card-shadow">
                        <Statistic title="Total sent for the month" value={smsLogs?.countMonthlySent} />
                    </Card>
                </Col>
                <Col xl={6}>
                    <Card size="small" bordered={false} loading={isLoading} style={{borderLeft: 'solid 5px limegreen'}} className="card-shadow">
                        <Statistic title="Total sent for the year" value={smsLogs?.countYearlySent} />
                    </Card>
                </Col>
        </Row>
        
        <Space>
            <Button type="danger" onClick={handleOpenBulkMessageDrawer}> <SendOutlined/> Send Bulk Message</Button>
        </Space>

        <Card title={<>Recent Messages Today</>} size="small" bordered={true} className="card-shadow mt-3">
            <RecentMessage data={data?.data ?? []} loading={isLoading}/>
        </Card>
        
        <Card title={<>Sms Logs </>} size="small" bordered={true} className="card-shadow mt-3">
            
            <SmsLogs data={smsLogs?.data ?? []} loading={smsLogsLoading} />
            
        </Card>

        <Drawer
                title="Send Bulk Message"
                placement="right"
                onClose={handleCloseBulkMessageDrawer}
                visible={openSendBulkDrawer}
                width={620}
            >

            <Form layout="vertical" form={bulkMessageForm} autoComplete="off" onFinish={handleSendBulkMessage}>
                
                <Row gutter={[5,0]}>

                    <Col md={24}>
                        <Form.Item name="event_lists" label="Select Events" rules={[{ required: true, message: 'Event is required' }]}>
                            <Select options={getEvents()} loading={smsEventLoading} onChange={handleSmsEvents} size="large"/>
                        </Form.Item>
                    </Col>

                    {
                        
                        Object.keys(selectedEventData).length > 0 ?

                            <Col md={24}>

                            <Descriptions bordered title="Recipients" size="small" column={2}>
                                {
                                    selectedEventData.recepients && selectedEventData.recepients.length > 0 ?
                                    <>
                                        {selectedEventData.recepients.map((item, index) => (
                                            <>

                                                <Descriptions.Item label="Recipient #" key={index}>
                                                    <strong>{ item.recepient }</strong>
                                                </Descriptions.Item>

                                                <Descriptions.Item label="Recipient name" key={index}>
                                                    <strong>{ item.recepient_name }</strong>
                                                </Descriptions.Item>
                                            
                                            </>
                                        ))}
                                    </> 
                                    : null
                                }
                            </Descriptions>
                            
                            <Descriptions bordered title="Details" size="small" column={1} className="mt-3">
                                <Descriptions.Item label="Event Name">
                                    <strong>{ selectedEventData.event_name }</strong>
                                </Descriptions.Item>
                                <Descriptions.Item label="Event Description">
                                    <strong>{ selectedEventData.event_description }</strong>
                                </Descriptions.Item>
                                <Descriptions.Item label="Message">
                                    <strong>{ selectedEventData.message }</strong>
                                </Descriptions.Item>
                            </Descriptions>
                        </Col>
                        : null
                    }

                    <Col md={24} className="mt-5">
                        <Form.Item name="sending_type" label="Sending Type" rules={[{ required: true, message: 'Sending Type is required' }]}>
                            <Select options={events} size="large" onChange={handleEventChange} />
                        </Form.Item>
                    </Col>

                    {
                        selectedEvent && selectedEvent === 'schedule' ?
                        <Col md={24}>
                            <Form.Item name="schedule_date" label="Set Schedule Date" rules={[{ required: true, message: 'Schedule date is required' }]}>
                              <DatePicker style={{width: "100%"}} size="large" showToday showTime />
                            </Form.Item>
                        </Col>
                        :
                        null
                    }

                </Row>
                
                <Button type="danger" style={{float: "right"}} loading={sendBulkMessageQueryLoading} size="middle" htmlType="submit"><SendOutlined/> Send</Button>
                
            </Form>

        </Drawer>

    </>
  );
}

export default Page;