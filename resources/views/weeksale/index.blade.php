@extends('master')
@section('script')
    <script src="https://unpkg.com/react@16/umd/react.development.js"></script>
    <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/babel-standalone@6.15.0/babel.min.js"></script>
@endsection
@section('mainContent')
    <div id="root"></div>
    <script type="text/babel">
        class Product extends React.Component{
            render(){
                return <tr>{
                    this.props.detail.map((key,value)=><td>{value}</td>)
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
                    return <table className="table table-bordered">{
                        this.state.products.map((key,data)=><Product sid={key} detail={data} />)
                    }</table>;
                }




            }
        }

        ReactDOM.render(
            <Sales url="http://italker.info/warranty/weekendsale/products"/>,
            document.getElementById('root')
        );

    </script>
@endsection