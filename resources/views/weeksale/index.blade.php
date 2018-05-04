@extends('master')
@section('script')
    <script src="https://unpkg.com/react@16/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/babel-standalone@6.15.0/babel.min.js"></script>
@endsection
@section('mainContent')
    <div class="col-md-12">

        <div class="panel panel-info" id="root">
            <div class="panel-heading">
                <h3>Product List</h3>
            </div>
            <div class="panel-body">
            </div>
        </div>
    </div>
    <script type="text/babel">
        class Product extends React.Component{
            render(){
                const product_info = this.props.detail;
                return <tr>{
                    Object.keys(product_info).map((key)=><td>{product_info[key]}</td>)
                }</tr>
            }
        }

        class Sales extends React.Component {
            constructor(props){
                super(props);
                this.state = {
                    error: null,
                    isLoaded: false,
                    products: []
                }
            }

            componentDidMount(){
                fetch(this.props.url)
                    .then(res => res.json())
                    .then(
                        (result)=>{
                            console.log(result);
                            this.setState({
                                isLoaded: true,
                                products: result
                            });

                        },
                        (error)=>{
                            this.setState({
                                isLoaded: false,
                                error: error
                            });
                        }

                    )
            }


            render(){
                const {error, isLoaded, products} = this.state;
                if (error){
                    return <div>Error: {error.message}</div>;
                }else if(!isLoaded){
                    return <div>Loading...</div>;
                }else{
                    return <table className="table table-bordered">
                        <thead>
                        <tr>
                            <th>Model</th>
                            <th>Name</th>
                            <th>price_current</th>
                            <th>special_current</th>
                            <th>cost</th>
                            <th>stock</th>
                            <th>lock_status</th>
                        </tr>
                        </thead>
                        <tbody>
                        {
                            products.map( (key,data) => (<Product detail={data} id={key} />))
                        }
                        </tbody>
                    </table>;
                }




            }
        }

        ReactDOM.render(
            <Sales url="{{url('weekendsale/products')}}"/>,
            document.getElementById('root')
        );

    </script>
@endsection