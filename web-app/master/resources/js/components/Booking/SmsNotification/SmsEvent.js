import React, { useEffect, useState } from 'react'
import { Row, Col, Card, Table, Menu, Button, Dropdown, Space, Drawer, Form, Input, message, Descriptions} from 'antd'
import { MoreOutlined, PlusOutlined, MinusCircleOutlined, EllipsisOutlined, EditOutlined} from '@ant-design/icons'
import SmsNotificationServices from 'services/Booking/SmsNotificationServices'

function SmsEvent(props) {
    const { data, loading, refetch } = props;

    const { TextArea } = Input;

    const [editEventForm, setOpenEventForm] = useState(false);

    const [viewEventForm, setOpenViewEvent] = useState(false);

    const [viewEventData, setViewData] = useState([]);

    const [eventForm] = Form.useForm();

    const [updateEvent, { isLoading: updateEventLoading}] = SmsNotificationServices.updateSmsEvent();

    const [removeEvent, { isLoading: removeEventLoading}] = SmsNotificationServices.removeSmsEvent();

    const onUpdateEvent = values => {

        if(updateEventLoading) return false;

        updateEvent({...values}, {
            onSuccess: (res) => {
                message.success('Event successfully updated!');
                setOpenEventForm(false);
                refetch();
            },
            onError: (e) => {
                message.error(e.message);
            }
        });
    }

    const deleteEvent = id => {

        if(removeEventLoading) return false;

        removeEvent({id: id}, {
            onSuccess: (res) => {
                message.success('Event successfully deleted!');
                refetch();
            },
            onError: (e) => {
                message.error(e.message);
            }
        });
    }

    const columns = [
        {
            title: "#",
            render: (text, record) => {
                return record.id;
            }
        },
        {
            title: "Event name",
            render: (text, record) => {
                return record.event_name;
            }
        },

        {
            title: "Event Description",
            render: (text, record) => {
                return record.event_description;
            }
        },

        {
            title: "Created At",
            render: (text, record) => {
                return record.created_at;
            }
        },

        {
            title: "Updated At",
            render: (text, record) => {
                return record.updated_at;
            }
        },

        {
            title: 'Actions',
            align: 'center',
            render: (text, record) => {
                return (
                    <>
                        <Space>
                            <Dropdown
                                    overlay={
                                        <Menu>
                                            <Menu.Item onClick={ () => {
                                                 setOpenViewEvent(true);
                                                 setViewData({
                                                     recepients: JSON.parse(record.recepients),
                                                     event_name: record.event_name,
                                                     message: record.message,
                                                     event_description: record.event_description
                                                 })
                                            }}>View Event</Menu.Item>
                                            <Menu.Item onClick={()=> {
                                                 setOpenEventForm(true);

                                                 eventForm.setFieldsValue({
                                                     id: record.id,
                                                     recepients: JSON.parse(record.recepients),
                                                     event_name: record.event_name,
                                                     message: record.message,
                                                     event_description: record.event_description
                                                 })
                                            }}>Edit</Menu.Item>
                                            <Menu.Item disabled={record?.event_history.length ? true : false} onClick={()=> {
                                                if(!confirm('Are you sure you want to delete this event?')) return false;

                                                deleteEvent(record.id)
                                            }}>Remove</Menu.Item>
                                        </Menu>
                                    }
                                    trigger={['click']}>
                                <Button icon={<EllipsisOutlined/>}/>
                            </Dropdown>
                        </Space>
                    </>
                );
            }
        }
    ];
  return (
     <>
           <Table
                size="small"
                rowKey="id"
                dataSource={data}
                loading={loading}
                columns={columns}
            />

            <Drawer
                title="Edit Event Form"
                placement="right"
                closable={true}
                onClose={() => {
                    setOpenEventForm(false);
                }}
                visible={editEventForm}
                width={620}
            >

            <Form layout="vertical" form={eventForm} autoComplete="off" onFinish={onUpdateEvent}>
                
                <Row gutter={[5,0]}>
                    <Col md={24}>
                        <Form.List name="recepients" initialValue={[{recepient: ''}]}>
                            {(fields, { add, remove }) => (
                                <>
                                    {fields.map(({ key, name, ...restField }) => (
                                        <Space
                                            key={key}
                                            style={{
                                                display: 'flex',
                                                marginBottom: 8,
                                            }}
                                            align="baseline"
                                        >
                                        <Form.Item
                                                {...restField}
                                                name={[name, 'recepient']}
                                            
                                                rules={[
                                                    {
                                                        required: true,
                                                        message: 'Recepient # is required',
                                                    },
                                                    {
                                                        pattern: /^(09|\+639)\d{9}$/,
                                                        message: 'Please enter a valid Philippine mobile number',
                                                    },
                                                ]}
                                            >
                                                <Input style={{width: '250px'}} placeholder="Recepient #" />
                                        </Form.Item>

                                        
                                        <Form.Item
                                                {...restField}
                                                name={[name, 'recepient_name']}
                                            
                                                rules={[
                                                    {
                                                        required: true,
                                                        message: 'Recepient name is required',
                                                    },
                                                ]}
                                            >
                                                <Input style={{width: '270px'}} placeholder="Recepient name" />
                                        </Form.Item>

                                          {
                                            fields.length > 1 ?
                                            <MinusCircleOutlined onClick={() => remove(name)} />  :
                                            null
                                          }

                                        </Space>
                                    ))}
                                    <Form.Item>
                                        <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                                           Add Recepient
                                        </Button>
                                    </Form.Item>
                                </>
                            )}
                        </Form.List>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="event_name" label="Event Name" rules={[{ required: true, message: 'Event name is required' }]}>
                            <Input size="large"/>
                        </Form.Item>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="event_description" label="Event Description">
                            <TextArea size="large"/>
                        </Form.Item>
                    </Col>

                    <Col md={24}>
                        <Form.Item name="message" label="Message" rules={[{ required: true, message: 'message is required' }]}>
                            <TextArea size="large"/>
                        </Form.Item>
                    </Col>
                    
                    <Form.Item name="id">
                        <Input type="hidden" />
                    </Form.Item>
                </Row>
                
                <Button type="danger" loading={updateEventLoading} style={{float: "right"}} size="middle" htmlType="submit">Update Event</Button>
                
            </Form>

        </Drawer>

        <Drawer
                title="View Event"
                placement="right"
                closable={true}
                onClose={() => {
                    setOpenViewEvent(false);
                }}
                visible={viewEventForm}
                width={620}
            >

            <Descriptions bordered title="Recepients" size="small" column={2}>
                {
                    viewEventData.recepients && viewEventData.recepients.length > 0 ?
                    <>
                        {viewEventData.recepients.map((item, index) => (

                            <>
                                <Descriptions.Item label="Recepient #" key={index}>
                                    <strong>{ item.recepient }</strong>
                                </Descriptions.Item>

                                <Descriptions.Item label="Recepient name" key={index}>
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
                    <strong>{ viewEventData.event_name }</strong>
                </Descriptions.Item>
                <Descriptions.Item label="Event Description">
                    <strong>{ viewEventData.event_description }</strong>
                </Descriptions.Item>
                <Descriptions.Item label="Message">
                    <strong>{ viewEventData.message }</strong>
                </Descriptions.Item>
            </Descriptions>
                

        </Drawer>
     </>
  );
}

export default SmsEvent;