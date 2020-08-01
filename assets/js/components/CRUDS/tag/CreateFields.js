//REACT
import React, {useContext, useState} from 'react';
import PropTypes from 'prop-types';
//CONTEXT
import {TagContext} from '../../../contexts/TagContext';
//MUI COMPONENTS
import {Grid, TextField, Box, IconButton, useTheme, useMediaQuery, Button} from '@material-ui/core';
//MUI ICONS
import {Add as AddIcon, Refresh as RefreshIcon} from '@material-ui/icons';

/**
 *
 * @param props
 * @param {[]} props.textFields
 * @param {string} props.textFields.name - name should match the key of an entity
 * @param {string} props.textFields.label - any string
 * @param {string} props.textFields.type - text | number
 * @example [{name: 'name', label: 'label', type: 'text'}]
 * @returns {*}
 * @constructor
 */
const CreateFields = (props) => {
    const context = useContext(TagContext);

    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('xs'));

    const {textFields} = props;
    const initialState = {};

    function checkType(type) {
        switch (type) {
            case 'text':
                return '';
            case 'number':
                return 0;
            default:
                console.error('Unknown type given');
                return;
        }
    }

    textFields.forEach(item => initialState[item.name] = item.type ? checkType(item.type) : '');

    const [state, setState] = useState(initialState);

    const handleChange = (e) => {
        setState({
            ...state,
            [e.target.name]: e.target.value,
        });
    };

    const onSubmit = (e) => {
        e.preventDefault();
        context.create(state);
        setState(initialState);
    };

    return (
        <form noValidate onSubmit={onSubmit}>
            <Box my={1}>
                <Grid container spacing={1} alignItems="center">
                    {textFields.map((item, index) => (
                        <Grid key={item.name} item xs={12} sm>
                            <TextField variant="outlined"
                                       size={isMobile ? 'medium' : 'small'}
                                       type="text"
                                       value={state[item.name]}
                                       label={item.label}
                                       name={item.name}
                                       fullWidth
                                       autoFocus={index === 0}
                                       onChange={handleChange}
                            />
                        </Grid>
                    ))}
                    <Grid item xs={12} sm={'auto'}>
                        {isMobile ?
                            <>
                                <Grid container spacing={1}>
                                    <Grid item xs>
                                        <Button fullWidth size="large" variant="contained" color="primary"
                                                onClick={onSubmit}>
                                            Add Tag {state.name}
                                        </Button>
                                    </Grid>

                                    <Grid item xs={'auto'}>
                                        < IconButton color='inherit' onClick={context.read}>
                                            <RefreshIcon/>
                                        </IconButton>
                                    </Grid>
                                </Grid>
                            </>
                            :
                            <>
                                <IconButton type="submit" color="primary" onClick={onSubmit}>
                                    <AddIcon/>
                                </IconButton>
                                < IconButton color='inherit' onClick={context.read}>
                                    <RefreshIcon/>
                                </IconButton>
                            </>
                        }
                    </Grid>
                </Grid>
            </Box>
        </form>
    );
};

CreateFields.propTypes = {
    textFields: PropTypes.arrayOf(PropTypes.shape({
        name:  PropTypes.string,
        label: PropTypes.string,
        type:  PropTypes.oneOf(['text', 'number']),
    })),
};

CreateFields.defaultProps = {
    textFields: [
        {
            name:  'defaultName',
            label: 'defaultLabel',
            type:  'text',
        },
    ],
};

export default CreateFields;